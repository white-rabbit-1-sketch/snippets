#include <ctime>
#include <iostream>
#include <string>
#include <array>
#include <boost/bind/bind.hpp>
#include <boost/shared_ptr.hpp>
#include <boost/enable_shared_from_this.hpp>
#include <boost/asio.hpp>

#include "TcpServer.hpp"

void handleConnectionWrite(const boost::system::error_code& error, size_t bytesTransferred)
{

}

void broadcast(
    boost::shared_ptr<TcpConnection> connection, 
    const boost::system::error_code& error,
    size_t bytesTransferred
) {
    if (connection->readBuffer.size()) {
        std::string message(connection->readBuffer.data(), bytesTransferred);    

        for (boost::shared_ptr<TcpConnection> serverConnection : connection->getTcpServer().getConnectionList()) {
            boost::asio::async_write(
                serverConnection->getSocket(),
                boost::asio::buffer(message, bytesTransferred),
                boost::bind(
                    &handleConnectionWrite,
                    boost::asio::placeholders::error,
                    boost::asio::placeholders::bytes_transferred
                )
            );
        }
    }

    connection->getSocket().async_read_some(
        boost::asio::buffer(connection->readBuffer),
        boost::bind(
            &broadcast,
            connection,
            boost::asio::placeholders::error,
            boost::asio::placeholders::bytes_transferred
        )
    );
}

void handleConnection(boost::shared_ptr<TcpConnection> connection) 
{
    boost::system::error_code errorCode;
    broadcast(connection, errorCode, 0);
}

int main()
{
    try {
        boost::asio::io_context ioContext;
        TcpServer tcpServer(ioContext, handleConnection);
        ioContext.run();
    } catch (std::exception& e) {
        std::cerr << e.what() << std::endl;
    }

    return 0;
}