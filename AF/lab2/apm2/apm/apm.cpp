#include <iostream>
#include <fstream>
#include <queue>
#include <tuple>
#include <vector>
#include <numeric>
#include <stdint.h>
using namespace std;

ifstream fin("apm.in");
ofstream fout("apm.out");

struct Link;
struct Node;
ifstream& operator >>(ifstream& in, Link& l);
ofstream& operator <<(ofstream& out, const Link& l);
void unite(int node1, int node2, vector<int>& parents, vector<int>& heights);
bool operator<(const Link& l1, const Link& l2);
int getReprez(int node, const vector<int>& parents);

int main(){
    int nr_nodes, nr_links;
    fin >> nr_nodes >> nr_links;

    vector<int> parents(nr_nodes + 1, 0),
                heights(nr_nodes + 1, 0);
    priority_queue<Link> ordered_links;
    
    for (int i = 0; i < nr_links; ++i){
        Link curr_link;
        fin >> curr_link;
        ordered_links.push(curr_link);
    }

    vector<Link> chosen_links;
    vector<bool> visited(nr_nodes + 1, false);
    int64_t min_sum = 0;

    while (chosen_links.size() < nr_nodes - 1 && !ordered_links.empty()){
        Link curr_link = ordered_links.top();
        int rep1 = getReprez(curr_link.start, parents),
            rep2 = getReprez(curr_link.end, parents);
        if (rep1 != rep2){
            unite(curr_link.start, curr_link.end, parents, heights);
            chosen_links.push_back(curr_link);
            min_sum += curr_link.weight;
        }
        ordered_links.pop();
    }
    
    fout << min_sum << '\n' << chosen_links.size() << '\n';
    for (const auto& link : chosen_links)
        fout << link << '\n';
}

int getReprez(int node, const vector<int>& parents){
    int curr_parent = parents[node];
    while (curr_parent){
        node = curr_parent;
        curr_parent = parents[node];
    }
    return node;
}

struct Link{
    int start, end, weight;
};


ifstream& operator >>(ifstream& in, Link& l){
    in >> l.start >> l.end >> l.weight;

    return in;
}

ofstream& operator <<(ofstream& out, const Link& l){
    out << l.start << " " << l.end;

    return out;
}

void unite(int node1, int node2, vector<int>& parents, vector<int>& heights){
    if (heights[node1] < heights[node2])
        parents[node1] = node2;
    else if (heights[node1] > heights[node2])
        parents[node2] = node1;
    else{
        parents[node1] = node2;
        heights[node2]++;
    }
}

bool operator<(const Link& l1, const Link& l2){
    return l1.weight > l2.weight;
}
