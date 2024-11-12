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
ifstream& operator >>(ifstream& in, Link& l);
ofstream& operator <<(ofstream& out, const Link& l);
void unite(int node1, int node2, vector<int>& colors);
bool operator<(const Link& l1, const Link& l2);


int main(){
    int nr_nodes, nr_links;
    fin >> nr_nodes >> nr_links;

    vector<int> colors(nr_nodes + 1, 0);
    iota(colors.begin(), colors.end(), 0);

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
        if (colors[curr_link.start] != colors[curr_link.end]){
            unite(curr_link.start, curr_link.end, colors);
            chosen_links.push_back(curr_link);
            min_sum += curr_link.weight;
        }
        ordered_links.pop();
    }
    fout << min_sum << '\n' << chosen_links.size() << '\n';
    for (const auto& link : chosen_links)
        fout << link << '\n';
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

void unite(int node1, int node2, vector<int>& colors){
    for (int i = 1; i < colors.size(); ++i)
        if (colors[i] == colors[node2] && i != node2)
            colors[i] = colors[node1];
    
    colors[node2] = colors[node1];
}

bool operator<(const Link& l1, const Link& l2){
    return l1.weight > l2.weight;
}
