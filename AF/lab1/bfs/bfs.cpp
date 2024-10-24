#include <iostream>
#include <vector>
#include <queue>
#include <unordered_set>
#include <fstream>
using namespace std;

ifstream fin("bfs.in");
ofstream fout("bfs.out");

vector<int> bfs(int start, const vector<vector<int>>& addiacence, unordered_set<int>& visited){
    vector<int> lengths(addiacence.size(), -1);
    queue<int> to_visit;

    to_visit.push(start);
    visited.insert(start);
    lengths[start] = 0;

    while(!to_visit.empty()){
        int curr = to_visit.front();
        for (int neighbour : addiacence[curr])
            if (visited.find(neighbour) == visited.end()){
                visited.insert(neighbour);
                lengths[neighbour] = lengths[curr] + 1;
                to_visit.push(neighbour);
            }
        to_visit.pop();
    }
    return lengths;
}


int main(){
    int nr_nodes, nr_links, start;
    fin >> nr_nodes >> nr_links >> start;

    vector<vector<int>> addiacence(nr_nodes + 1);
    for (int i = 0; i < nr_links; ++i){
        int node1, node2;
        fin >> node1 >> node2;
        addiacence[node1].push_back(node2);
    }

    unordered_set<int> visited;
    vector<int> lengths = bfs(start, addiacence, visited);
    for (int i = 1; i < lengths.size(); ++i)
        fout << lengths[i] << " ";
}