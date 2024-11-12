#include <iostream>
#include <stdint.h>
#include <unordered_map>
#include <string>
#include <vector>
using namespace std;


uint32_t parseIP(string ip);
uint32_t getFirstNBits(uint32_t val, int n);
uint32_t getMask(int n);
bool searchIp(string ip, const unordered_map<string, uint32_t>& ip_map);

int main(){
    int n;
    cin >> n;

    unordered_map<string, uint32_t> ip_map;
    vector<bool> results;
    for (int i = 0; i < n; ++i){
        int op;
        cin >> op;

        string ip;
        cin >> ip;

        if (op == 1)
            ip_map[ip]++;
        else if (op == 2)
            ip_map[ip]--;
        else 
            results.push_back(searchIp(ip, ip_map));
    }

    for (bool r : results)
        if (r)
            cout << "Da\n";
        else
            cout << "Nu\n";
}

bool verifInRange(string ip_range, string ip){
    int pos = ip_range.find('/');
    int nr = stoi(string(ip_range.begin() + pos + 1, ip_range.end()));

    string aux_ip = string(ip_range.begin(), ip_range.begin() + pos);

    uint32_t x1 = getFirstNBits(parseIP(aux_ip), nr),
             x2 = getFirstNBits(parseIP(ip), nr);
    return x1 == x2;
}

uint32_t parseIP(string ip){
    int nr = 3;
    uint32_t res = 0;

    int curr_pos = 0;
    int end_pos = ip.find('.');
    string :: iterator start = ip.begin();

    while (end_pos != string :: npos){
        string aux(start + curr_pos, start + end_pos);
        res += stoi(aux) << (nr * 8);
        curr_pos = end_pos + 1;
        end_pos = ip.find('.', curr_pos);
    }
    
    res += stoi(string(start + curr_pos, ip.end()));
    return res;
}

uint32_t getFirstNBits(uint32_t val, int n){
    return val & getMask(n);
}

uint32_t getMask(int n){
    uint32_t res = 0;
    uint32_t bit_pos = 31;
    for (int i = 0; i < n; ++i)
        res += 1 << bit_pos--;
    
    return res;
}


bool searchIp(string ip, const unordered_map<string, uint32_t>& ip_map){
     for (const auto& elem : ip_map)
        if (elem.second > 0 && verifInRange(elem.first, ip))
            return true;
    return false;
}