#include <iostream>
#include <vector>
#include <queue>
#include <unordered_set>
#include <fstream>
using namespace std;

ifstream fin("ciclic.in");
ofstream fout("ciclic.out");

vector<int> dfs(int start, const vector<vector<int>>& addiacence, unordered_set<int>& visited, vector<int> cycle, int father){
    visited.insert(start);
    cycle.push_back(start);
    
    for (int i = 1; i < addiacence.size(); ++i)
        if (addiacence[start][i])
            if (visited.find(i) == visited.end()){
                vector<int> res = dfs(i, addiacence, visited, cycle, start);
                if (!res.empty())
                    return res;
            }
            else if (father != i){
                cycle.push_back(i);
                return cycle;
            }
    return vector<int>();
}


int main(){
    int nr_nodes, nr_links;
    fin >> nr_nodes >> nr_links;

    vector<vector<int>> addiacence(nr_nodes + 1, vector<int>(nr_nodes + 1));
    for (int i = 0; i < nr_links; ++i){
        int node1, node2;
        fin >> node1 >> node2;
        addiacence[node1][node2] = addiacence[node2][node1] = 1;
    }

    unordered_set<int> visited;
    for (int i = 1; i <= nr_nodes; ++i)
        if (visited.find(i) == visited.end()){
            vector<int> res = dfs(i, addiacence, visited, vector<int>(), 0);

            if (!res.empty()){
                for (int i = 1; i < res.size(); ++i)
                    fout << res[i] << " ";
                return 0;
            }
        }
    fout << -1;
}