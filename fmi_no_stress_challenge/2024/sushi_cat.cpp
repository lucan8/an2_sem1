#include <iostream>
#include <vector>
#include <stdint.h>
#include <queue>
#include <unordered_map>
using namespace std;

struct fractie{
    uint64_t numarator, numitor;
    fractie operator*(const fractie& other){
        return fractie(numarator * other.numarator, numitor * other.numitor);
    }
    fractie(uint64_t x = 1, uint64_t y = 1) : numarator(x), numitor(y){}
    
};

uint64_t magic_nr = 1000000000 + 7;

vector<pair<fractie, bool>> getProb(const vector<unordered_map<uint64_t, uint64_t>>& addiacence, int start){
    queue<uint64_t> q;
    q.push(start);
    vector<bool> viz(addiacence.size(), false);
    viz[start] = true;

    vector<pair<fractie, bool>> res(addiacence.size(), make_pair(fractie(1, 1), false));

    while (!q.empty()){
        uint64_t curr = q.front();
        bool is_leaf = true;
        for (const auto& vecin : addiacence[curr])
            if (vecin.first != -1 && !viz[vecin.first]){
                res[vecin.first].first = res[curr].first * fractie(vecin.second, addiacence[curr].at(-1));
                viz[vecin.first] = true;
                q.push(vecin.first);
                is_leaf = false;
            }
        
        res[curr].second = is_leaf;
        q.pop();
    }
    return res;
}

void makeSums(vector<unordered_map<uint64_t, uint64_t>>& addiacence){
    queue<uint64_t> q;
    q.push(1);
    vector<bool> viz(addiacence.size(), false);
    viz[1] = true;

    while (!q.empty()){
        int curr = q.front();
        for (const auto& vecin : addiacence[curr])
            if (vecin.first != -1 && !viz[vecin.first]){
                viz[vecin.first] = true;
                q.push(vecin.first);
                addiacence[curr][-1] += vecin.second;
            }
        q.pop();
    }
   
}
int main(){
    int nr_nodes;
    uint64_t nr_reps;
    cin >> nr_nodes >> nr_reps;

    vector<unordered_map<uint64_t, uint64_t>> addiacence(nr_nodes + 1);
    for (int i = 0; i < nr_nodes - 1; ++i){
        uint64_t x, y, weight;
        cin >> x >> y >> weight;
        addiacence[x][y] = weight;
        addiacence[y][x] = weight;
    }
    makeSums(addiacence);
    vector<pair<fractie, bool>> res = getProb(addiacence, 1);
    if (addiacence[1].size() == 2)
        cout << 0 << endl;

    for (const auto& r : res)
        if (r.second)
            cout << (magic_nr + 1) / r.first.numitor * r.first.numarator << endl; 
}