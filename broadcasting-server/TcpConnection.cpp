#include "TcpConnection.hpp"

boost::shared_ptr<TcpConnection> TcpConnection::create(boost::asio::io_context &ioContext, TcpServer &tcpServer)
{
    return boost::shared_ptr<TcpConnection>(new TcpConnection(ioContext, tcpServer));
}

boost::asio::ip::tcp::socket &TcpConnection::getSocket()
{
    return this->socket;
}

TcpServer &TcpConnection::getTcpServer()
{
    return this->tcpServer;
}

TcpConnection::TcpConnection(boost::asio::io_context &ioContext, TcpServer &tcpServer): socket(ioContext), tcpServer(tcpServer)
{
    boost::system::error_code ec;
    socket.native_non_blocking(true, ec);
}