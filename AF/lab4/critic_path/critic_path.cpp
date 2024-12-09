#include <iostream>
#include <fstream>
#include <vector>
#include <queue>
#include <memory>
using namespace std;

struct Node;
typedef vector<shared_ptr<Node>> shared_nodes;
typedef shared_ptr<Node> shared_node;

struct Node{
    int duration, finish_time;
    bool visited, starting, critical;
    shared_nodes neighbours;

    Node(int duration = 0, int finish_time = 0, bool visited = false, bool critical = false,
         bool starting = true, const shared_nodes& neighbours = shared_nodes())
        : duration(duration), finish_time(finish_time), visited(visited), critical(critical),
          starting(starting), neighbours(neighbours){}
    // int drag, fp, duration;
    // pair<int, int> eariliest_interval;
    // pair<int, int> latest_interval;
};

void dij(shared_node& start){
    queue<shared_node> q;
    q.push(start);
    start->visited = true;
    start->finish_time = start->duration;

    while (!q.empty()){
        shared_node curr = q.front();
        for (auto& neigh : curr->neighbours){
            if (!neigh->visited){
                q.push(neigh);
                neigh->finish_time = curr->finish_time + neigh->duration;
                //neigh->start_time = curr->finish_time;
            }
            else{
                neigh->finish_time = min(neigh->finish_time, curr->finish_time + curr->duration);
                //neigh->start_time = min(neigh->start_time, curr->finish_time);
            }
        }
        q.pop();
    }
}

int main(){
    ifstream fin("activitati.in");
    ofstream fout("activitati.out");

    if (!fin){
        cout << "Could not open input file!\n";
        return -1;
    }
    int nr_activities;
    fin >> nr_activities;

    shared_nodes activities(nr_activities + 1);
    for (int i = 1; i < activities.size(); ++i){
        activities[i] = shared_node(new Node());
        fin >> activities[i]->duration;
    }
    
    int nr_edges;
    fin >> nr_edges;

    for (int i = 0; i < nr_edges; ++i){
        int act_before, act_after;
        fin >> act_before >> act_after;

        activities[act_before]->neighbours.push_back(activities[act_after]);
        activities[act_after]->starting = false;
    }

    for (auto& act : activities)
        if (act && act->starting)
            dij(act);
    
    for (auto& act : activities)
        if (act)
            fout << act->finish_time - act->duration << " " << act->finish_time << '\n';
}