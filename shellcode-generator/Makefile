BUILD_DIR = ./build

CC = g++
CFLAGS  = -g -Wall -std=c++17
LDFLAGS = 

default: main

main: main.o
	$(CC) $(CFLAGS) -o $(BUILD_DIR)/main $(BUILD_DIR)/main.o $(LDFLAGS)

main.o: main.cpp
	$(CC) $(CFLAGS) -c main.cpp -o $(BUILD_DIR)/main.o

clean:
	$(RM) $(BUILD_DIR)/*

.PHONY: default clean
