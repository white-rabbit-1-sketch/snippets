#include <iostream>
#include <string>
#include <boost/asio.hpp>
#include <chrono>

int main() {
    boost::asio::io_context ioContext;
    boost::asio::ip::tcp::socket socket(ioContext);
    boost::system::error_code errorCode;

    boost::asio::ip::tcp::endpoint serverEndpoint(boost::asio::ip::address::from_string("127.0.0.1"), 13);

    socket.connect(serverEndpoint, errorCode);
    if (errorCode) {
        std::cout << "Failed to connect to server: " << errorCode.message() << std::endl;
        return 1;
    }

    std::cout << "Starting write benchmark" << std::endl;
    std::chrono::system_clock::time_point start = std::chrono::system_clock::now();
    for (int i = 0; i < 1000; ++i) {
        std::string key = "key" + std::to_string(i);
        std::string value = "value" + std::to_string(i);

        std::string message = key + " " + value;

        boost::asio::write(socket, boost::asio::buffer(message), errorCode);
        if (errorCode) {
            std::cout << "Failed to send data: " << errorCode.message() << std::endl;
            return 1;
        }

        char buffer[1024];
        std::memset(buffer, 0, sizeof(buffer));
        size_t bytesRead = socket.read_some(boost::asio::buffer(buffer, sizeof(buffer)), errorCode);
        if (errorCode) {
            std::cout << "Failed to receive data: " << errorCode.message() << std::endl;
            return 1;
        }
    }
    std::chrono::system_clock::time_point end = std::chrono::system_clock::now();
    std::chrono::duration<double> diff = end - start;
    std::cout << "Completed in " << diff.count() << " seconds" << std::endl;

    std::cout << "Starting read benchmark" << std::endl;
    start = std::chrono::system_clock::now();
    for (int i = 0; i < 1000; ++i) {
        std::string message = "key" + std::to_string(i);

        boost::asio::write(socket, boost::asio::buffer(message), errorCode);
        if (errorCode) {
            std::cout << "Failed to send data: " << errorCode.message() << std::endl;
            return 1;
        }

        char buffer[1024];
        std::memset(buffer, 0, sizeof(buffer));
        size_t bytesRead = socket.read_some(boost::asio::buffer(buffer, sizeof(buffer)), errorCode);
        if (errorCode) {
            std::cout << "Failed to receive data: " << errorCode.message() << std::endl;
            return 1;
        }
    }
    end = std::chrono::system_clock::now();
    diff = end - start;
    std::cout << "Completed in " << diff.count() << " seconds" << std::endl;

    socket.close();

    return 0;
}
