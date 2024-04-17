#ifndef TCP_CONNECTION_H
#define TCP_CONNECTION_H

#include <boost/shared_ptr.hpp>
#include <boost/enable_shared_from_this.hpp>
#include <boost/asio.hpp>

class TcpServer;

class TcpConnection: public boost::enable_shared_from_this<TcpConnection>
{
    public:
        static boost::shared_ptr<TcpConnection> create(boost::asio::io_context &ioContext, TcpServer &tcpServer);
        boost::asio::ip::tcp::socket &getSocket();
        TcpServer &getTcpServer();
        

        std::array<char, 1024> readBuffer;
        std::array<char, 1024> writeBuffer;

    private:
        boost::asio::ip::tcp::socket socket;
        TcpServer &tcpServer;

        TcpConnection(boost::asio::io_context &ioContext, TcpServer &tcpServer);
};

#endif