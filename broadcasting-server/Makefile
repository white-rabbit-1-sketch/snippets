BUILD_DIR = ./build
BOOST_HEADER_DIR = /usr/local/include/boost
BOOST_LIB_DIR = /usr/lib

CC = g++
CFLAGS  = -g -Wall -std=c++17 -I$(BOOST_HEADER_DIR) -pthread
LDFLAGS = -L$(BOOST_LIB_DIR) -lboost_system -lboost_thread #-lboost_asio

default: main

main: main.o TcpServer.o TcpConnection.o
	$(CC) $(CFLAGS) -o $(BUILD_DIR)/main $(BUILD_DIR)/TcpConnection.o $(BUILD_DIR)/TcpServer.o $(BUILD_DIR)/main.o $(LDFLAGS)

main.o: main.cpp
	$(CC) $(CFLAGS) -c main.cpp -o $(BUILD_DIR)/main.o

TcpServer.o: TcpServer.cpp
	$(CC) $(CFLAGS) -c TcpServer.cpp -o $(BUILD_DIR)/TcpServer.o

TcpConnection.o: TcpConnection.cpp
	$(CC) $(CFLAGS) -c TcpConnection.cpp -o $(BUILD_DIR)/TcpConnection.o

clean:
	$(RM) $(BUILD_DIR)/*

.PHONY: default clean
