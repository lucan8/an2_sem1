#include <iostream>
#include <math.h>
#include <vector>
using namespace std;

vector<vector<int>>puteri(100000, vector<int>(1, 1));
int myPow(int n, int exp){
    if (exp < puteri[n].size())
        return puteri[n][exp];

    for (int i = puteri[n].size(); i <= exp; ++i)
        puteri[n].push_back(puteri[n].back() * n);
    return puteri[n][exp];
}

bool prietenoasa(int bila){
    int fp = 2;
    int all_p = 0;

    while (bila != 1){
        if (all_p){
            if(bila % fp == 0){
                int fp1 = myPow(fp, all_p);
                if (bila % fp1)
                    return false;
                else{
                    bila /= fp1;
                    if (bila % fp == 0)
                        return false;
                }
            }
        }
        else{
            int p = 0;
            while (bila % fp == 0){
                bila /= fp;
                p++;
            }
            all_p = p;
            myPow(fp, p);
        }
        fp++;
    }
    return true;
}

int main(){
    int n;
    cin >> n;

    vector<vector<int>>& puteri1 = puteri;
    int nr = 0;
    for (int i = 0; i < n; ++i){
        int bila;
        cin >> bila;
        bool res = prietenoasa(bila);
        if (res)
            nr++;
    }

    cout << (int)((1.0 * nr / n) * 100) << endl;
}