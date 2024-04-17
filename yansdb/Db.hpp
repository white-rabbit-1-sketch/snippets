#ifndef DB_H
#define DB_H

#include <iostream>
#include <map>

class Db
{
    public:
        std::string &getDataByKey(std::string &key);
        void setDataByKey(std::string &key, std::string &data);
    private:
        std::map<std::string, std::string> storage;
};

#endif