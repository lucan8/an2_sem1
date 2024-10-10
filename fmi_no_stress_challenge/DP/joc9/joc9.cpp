#include <iostream>
#include <fstream>
#include <vector>
#include <algorithm>
using namespace std;

int getWinningSumTop(vector<vector<int>> dp, const vector<vector<int>>& initial_dp);
int getWinningSumRight(vector<vector<int>> dp, const vector<vector<int>>& initial_dp);
int getWinningSumLeft(vector<vector<int>> dp, const vector<vector<int>>& initial_dp);

int main(){
    ifstream fin("joc9.in");
    ofstream fout("joc9.out");
    
    int n;
    fin >> n;
    vector<vector<int>> dp(n + 2, vector<int>(n + 2, 0));
    for (int i = 1; i <= n + 1; i++)
        for (int j = 1; j <= i; j++)
            fin >> dp[i][j];

    vector<pair<int, int>> results( {{getWinningSumTop(dp, dp), dp[1][1]},
                                     {getWinningSumLeft(dp, dp), dp.back()[1]},
                                     {getWinningSumRight(dp, dp), dp.back().back()}});
    cout << results.front().first << endl;
    pair<int, int> chosen = *std :: max_element(results.begin(), results.end(),
                                                [](const pair<int, int>& p1, const pair<int, int>& p2){
                                                    if (p1.first == p2.first)
                                                        return p1.second > p2.second;
                                                    return p1.first < p2.first;
                                                });
    fout << chosen.first << '\n' << chosen.second;
    //fout << results[0].first << '\n' << results[0].second;
    //fout << results[1].first << '\n' << results[1].second;
    //fout << results[2].first << '\n' << results[2].second;
}


int getWinningSumTop(vector<vector<int>> dp, const vector<vector<int>>& initial_dp){
    int max_sum = 0;
    
    for (int i = 1; i < dp.size() - 1; ++i)
        for (int j = 1; j <= i; ++j){
            dp[i + 1][j] = max(dp[i + 1][j], dp[i][j] + initial_dp[i + 1][j]);
            dp[i + 1][j + 1] = max(dp[i + 1][j + 1], dp[i][j] + initial_dp[i + 1][j + 1]);
            max_sum = max(max_sum, max(dp[i + 1][j], dp[i + 1][j + 1]));
        }
    return max_sum;
}

int getWinningSumRight(vector<vector<int>> dp, const vector<vector<int>>& initial_dp){
    int max_sum = 0;
    
    for (int i = dp.size() - 1; i >= 2; --i)
        for (int j = i; j >= 2; --j){
            dp[i][j - 1] = max(dp[i][j - 1], dp[i][j] + initial_dp[i][j - 1]);
            dp[i - 1][j - 1] = max(dp[i - 1][j - 1], dp[i][j] + initial_dp[i - 1][j - 1]);
            max_sum = max(max_sum, max(dp[i][j - 1], dp[i - 1][j - 1]));
        }
    return max_sum;
}


int getWinningSumLeft(vector<vector<int>> dp, const vector<vector<int>>& initial_dp){
    int max_sum = 0;
   
    for (int i = dp.size() - 1; i >= 2; --i)
        for (int j = 1; j <= i - 1; ++j){
            dp[i][j + 1] = max(dp[i][j + 1], dp[i][j] + initial_dp[i][j + 1]);
            dp[i - 1][j] = max(dp[i - 1][j], dp[i][j] + initial_dp[i - 1][j]);
            max_sum = max(max_sum, max(dp[i][j + 1], dp[i - 1][j]));
        }
    return max_sum;
}
