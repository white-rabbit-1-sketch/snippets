#ifndef TCP_SERVER_H
#define TCP_SERVER_H

#include <boost/bind/bind.hpp>
#include <boost/shared_ptr.hpp>
#include <boost/enable_shared_from_this.hpp>
#include <boost/asio.hpp>

#include "TcpConnection.hpp"

class TcpServer
{
    public:
        using ConnectionHandler = void (&)(boost::shared_ptr<TcpConnection>);

        TcpServer(boost::asio::io_context &ioContext, ConnectionHandler connectionHandler);
        std::vector<boost::shared_ptr<TcpConnection>> &getConnectionList();

    private:
        boost::asio::io_context &ioContext;
        boost::asio::ip::tcp::acceptor acceptor;
        ConnectionHandler connectionHandler;
        std::vector<boost::shared_ptr<TcpConnection>> connectionList;

        void listen();
        void acceptConnection(boost::shared_ptr<TcpConnection> connection, const boost::system::error_code &error);
};

#endif