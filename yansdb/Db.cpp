#include "Db.hpp"

std::string &Db::getDataByKey(std::string &key)
{
    return this->storage[key];
}

void Db::setDataByKey(std::string &key, std::string &data)
{
    this->storage[key] = data;
}