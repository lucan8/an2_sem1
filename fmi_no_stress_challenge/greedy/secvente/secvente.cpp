#include <iostream>
#include <fstream>
#include <vector>
using namespace std;

ifstream fin("secvente.in");
ofstream fout("secvente.out");

//First is the smaller element
pair<int, int> Min_Max(int v1, int v2);
//Reads n and seq and gets seq with max length that has sum % 3 == 0
int getMaxSeq();

int main(){
   fout << getMaxSeq() << endl << getMaxSeq() << endl << getMaxSeq();
}

int getMaxSeq(){
    int n;
    fin >> n;
    vector<vector<int>> v(3);
    v[0].reserve(n), v[1].reserve(n), v[2].reserve(n);

    int val;
    for (int i = 0; i < n; ++i){
        fin >> val;
        v[val % 3].push_back(val);
    }
    int max_seq = v[0].size();
    pair<int, int> min_max = Min_Max(v[1].size(), v[2].size());

    int min_max_diff = min_max.second - min_max.first;
    max_seq += max(2 * min_max.first + min_max_diff - min_max_diff % 3,
                   min_max_diff + 1 - (min_max_diff + 1) % 3);
    return max_seq;
}


pair<int, int> Min_Max(int v1, int v2){
    if (v1 < v2)
        return {v1, v2};
    return {v2, v1};
}