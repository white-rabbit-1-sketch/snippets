#include "TcpServer.hpp"

TcpServer::TcpServer(
    boost::asio::io_context &ioContext,
    ConnectionHandler connectionHandler
): ioContext(ioContext), acceptor(ioContext, boost::asio::ip::tcp::endpoint(boost::asio::ip::tcp::v4(), 13)), connectionHandler(connectionHandler)
{
    this->listen();
}

void TcpServer::listen()
{
    boost::shared_ptr<TcpConnection> connection = TcpConnection::create(this->ioContext, *this);

    this->acceptor.async_accept(
        connection->getSocket(),
        boost::bind(
            &TcpServer::acceptConnection, 
            this, 
            connection,
            boost::asio::placeholders::error
        )
    );
}

void TcpServer::acceptConnection(boost::shared_ptr<TcpConnection> connection, const boost::system::error_code &error)
{
    this->connectionList.push_back(connection);
    this->connectionHandler(connection);
    this->listen();
}

std::vector<boost::shared_ptr<TcpConnection>> &TcpServer::getConnectionList()
{
    return this->connectionList;
}