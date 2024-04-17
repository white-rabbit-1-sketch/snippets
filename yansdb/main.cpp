#include <ctime>
#include <iostream>
#include <string>
#include <array>
#include <boost/bind/bind.hpp>
#include <boost/shared_ptr.hpp>
#include <boost/enable_shared_from_this.hpp>
#include <boost/asio.hpp>

#include "Db.hpp"
#include "TcpServer.hpp"
#include "String.hpp"

Db db;

void handleConnectionWrite(const boost::system::error_code& error, size_t bytesTransferred)
{

}

void handleCommand(
    boost::shared_ptr<TcpConnection> connection, 
    const boost::system::error_code& error,
    size_t bytesTransferred
) {
    if (connection->readBuffer.size()) {
        std::string command(connection->readBuffer.data(), bytesTransferred);
        std::vector<std::string> commandPartList = explode(command, ' ');

        if (commandPartList.size() == 1) {
            std::string &data = db.getDataByKey(commandPartList[0]);
            boost::asio::async_write(
                connection->getSocket(),
                boost::asio::buffer(data, data.size()),
                boost::bind(
                    &handleConnectionWrite,
                    boost::asio::placeholders::error,
                    boost::asio::placeholders::bytes_transferred
                )
            );
        } else if (commandPartList.size() == 2) {
            db.setDataByKey(commandPartList[0], commandPartList[1]);

            boost::asio::async_write(
                connection->getSocket(),
                boost::asio::buffer("OK", bytesTransferred),
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
            &handleCommand,
            connection,
            boost::asio::placeholders::error,
            boost::asio::placeholders::bytes_transferred
        )
    );
}

void handleConnection(boost::shared_ptr<TcpConnection> connection) 
{
    boost::system::error_code errorCode;
    handleCommand(connection, errorCode, 0);
}

int main() {
    try {
        boost::asio::io_context ioContext;
        TcpServer tcpServer(ioContext, handleConnection);
        ioContext.run();
    } catch (std::exception& e) {
        std::cerr << e.what() << std::endl;
    }

    return 0;
}
