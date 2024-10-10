#include <iostream>
#include <fstream>
#include <algorithm>
#include <unordered_map>
#include <cstdint>
#include <random>
using namespace std;

uint64_t getDomenii1(const string& str);
uint64_t getDomenii2(const string& str);
string generateString();

int main(){
    ifstream fin("domenii.in");
    ofstream fout("domenii.out");

    for (int i = 0; i < 1000; ++i){
        string str = generateString();
        uint64_t res1 = getDomenii1(str), res2 = getDomenii2(str);
        if (res1 != res2)
            cout << res1 << "   " << res2 << '\n';
    }
    cout << "DONE\n";
}


uint64_t getDomenii1(const string& str){
    int n = str.size();
    uint64_t nr = 0;
    for (int i = 0; i < n - 2; ++i)
        if (str[i] == '.')
        for (int j = i + 1; j < n - 1; ++j)
            if (str[j] != '.')
                for (int k = j + 1; k < n; ++k)
                    if (str[k] != '.' && str[j] != str[k])
                        nr++;
    
    return nr;
    
}


uint64_t getDomenii2(const string& str){
    int n = str.size();
    uint64_t nr = 0;
    unordered_map<char, int> letter_freq;

    for (int i = 0; i < n; ++i)
        letter_freq[str[i]]++;
    
    for (int i = 0; i < n; ++i){
        if (str[i] == '.'){
            int nr_letters = n - letter_freq['.'] - i;
            nr += nr_letters * (nr_letters - 1) / 2;

            for (const auto& [letter, freq] : letter_freq)
                if (letter != '.' && freq > 1)
                    nr -= freq * (freq - 1) / 2;
        }
        letter_freq[str[i]]--;
    }
    return nr;
}


string generateString(){
    random_device rd;
    mt19937 gen(rd());
    uniform_int_distribution<int> string_dist(0, 26),
                                  size_dist(3000, 5000);
    int n = size_dist(gen);
    string str(n, ' ');
    for (int i = 0; i < n; ++i){
        int v = string_dist(gen);
        if (v == 26)
            str[i] = '.';
        else
            str[i] = 'a' + string_dist(gen);
    }
    return str;
}