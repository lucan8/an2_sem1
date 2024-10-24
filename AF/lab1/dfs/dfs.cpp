#include <iostream>
#include <vector>
#include <queue>
#include <unordered_set>
#include <fstream>
using namespace std;

ifstream fin("dfs.in");
ofstream fout("dfs.out");

void dfs(int start, const vector<vector<int>>& addiacence, unordered_set<int>& visited){
    visited.insert(start);
    for (int i = 1; i < addiacence.size(); ++i)
        if (visited.find(i) == visited.end() && addiacence[start][i])
            dfs(i, addiacence, visited);
}


int main(){
    int nr_nodes, nr_links;
    fin >> nr_nodes >> nr_links;

    vector<vector<int>> addiacence(nr_nodes + 1);
    vector<vector<int>> addiacence1(nr_nodes + 1, vector<int>(nr_nodes + 1));
    for (int i = 0; i < nr_links; ++i){
        int node1, node2;
        fin >> node1 >> node2;
        addiacence[node1].push_back(node2);
        addiacence[node2].push_back(node1);

        addiacence1[node1][node2] = addiacence1[node2][node1] = 1;
    }

    unordered_set<int> visited;
    int nr_components = 0;
    for (int i = 1; i <= nr_nodes; ++i)
        if (visited.find(i) == visited.end()){
            nr_components ++;
            dfs(i, addiacence1, visited);
        }
    fout << nr_components;
}