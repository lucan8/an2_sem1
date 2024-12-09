#include <iostream>
#include <fstream>
#include <memory>
#include <vector>
#include <queue>
#include <algorithm>
using namespace std;
struct Node;
typedef shared_ptr<Node> shared_node;
typedef pair<shared_node, int> neighbour_edge;

struct Node{
    int val;
    bool visited, control_node;

    int min_dist;
    shared_node parent;
    vector<neighbour_edge> neighbours_edges;

    Node(int val = 0, bool visited = false, bool control_node = false, int min_dist = INT_MAX,
        shared_node parent = shared_node(nullptr), const vector<neighbour_edge>& neighbours_edges = vector<neighbour_edge>())
        : val(val), visited(visited), control_node(control_node), min_dist(min_dist), parent(parent),
          neighbours_edges(neighbours_edges){}
};

//Created a vector of shared_nodes, unvisited, uncontrolled and with a value between 1-nr_nodes(unique values)
//The vector is indexed starting at 1 and ending at nr_nodes
vector<shared_node> createUninitializedNodes(int nr_nodes);

//Reads edges and creates a node vector containing all neighbour dependencies based on edges
//The value of each node is equal to the index of the vector
vector<shared_node> createNodes(ifstream& fin, int nr_nodes, int nr_edges);

//Reads the control nodes values from file and marks the corresponding ones from the vector
void markControlNodes(ifstream& fin, vector<shared_node>& nodes);

// Determines the minimum distance from start to each other node(and constructs the paths needed)
void dij(shared_node start, vector<shared_node>& nodes);

vector<shared_node> getControlNodes(const vector<shared_node>& nodes);

//Compared by min_dist
bool operator <(const shared_node& n1, const shared_node& n2);


int main(){
    ifstream fin("graph.in");
    ofstream fout("graph.out");

    if (!fin){
        cerr << "Could not open input file!\n";
        return -1;
    }

    int nr_nodes, nr_edges;
    fin >> nr_nodes >> nr_edges;

    vector<shared_node> nodes = createNodes(fin, nr_nodes, nr_edges);
    markControlNodes(fin, nodes);

    int start_node;
    fin >> start_node;
    dij(nodes[start_node], nodes);

    vector<shared_node> control_nodes = getControlNodes(nodes);
    shared_node chosen_node = *min_element(control_nodes.begin(), control_nodes.end());
    while (chosen_node->parent){
        cout << chosen_node->val << ' ';
        chosen_node = chosen_node->parent;
    }
    cout << start_node << endl;
}

void dij(shared_node start, vector<shared_node>& nodes){
    priority_queue<shared_node> nodes_q;
    nodes_q.push(start);
    start->visited = true;
    start->min_dist = 0;

    while (!nodes_q.empty()){
        shared_node curr = nodes_q.top();
        //Goint through each neighbour, relaxing the edges between curr and each neighbour
        for (const auto& neig_e : curr->neighbours_edges){
            //If using this edge decreases the min_dist of the neighbour we update min_dist and the parent to curr
            if (curr->min_dist + neig_e.second < neig_e.first->min_dist){
                neig_e.first->min_dist = curr->min_dist + neig_e.second;
                neig_e.first->parent = curr;
            }
            // If the node is not visited we visit it and push it into the priority queue
            if (!neig_e.first->visited){
                neig_e.first->visited = true;
                nodes_q.push(neig_e.first);
            }
        }
        nodes_q.pop();
    }

}

vector<shared_node> createUninitializedNodes(int nr_nodes){
    vector<shared_node> nodes;
    nodes.reserve(nr_nodes + 1);

    nodes.emplace_back(nullptr);
    for (int i = 1; i <= nr_nodes; ++i)
        nodes.emplace_back(new Node(i));

    return nodes;
}

vector<shared_node> createNodes(ifstream& fin, int nr_nodes, int nr_edges){
    vector<shared_node> nodes = createUninitializedNodes(nr_nodes);
    for (int i = 0; i < nr_edges; ++i){
        int node1, node2, weight;
        fin >> node1 >> node2 >> weight;

        nodes[node1]->neighbours_edges.emplace_back(nodes[node2], weight);
        nodes[node2]->neighbours_edges.emplace_back(nodes[node1], weight);
    }
    return nodes;
}


void markControlNodes(ifstream& fin, vector<shared_node>& nodes){
    int nr_control_nodes;
    fin >> nr_control_nodes;

    for (int i = 0; i < nr_control_nodes; ++i){
        int c_node;
        fin >> c_node;
        nodes[c_node]->control_node = true;
    }
}

vector<shared_node> getControlNodes(const vector<shared_node>& nodes){
    vector<shared_node> control_nodes;
    
    for (const auto& node: nodes){
        if (node && node->control_node)
            control_nodes.push_back(node);
    }
    return control_nodes;
}

bool operator <(const shared_node& n1, const shared_node& n2){
    return n1->min_dist < n2->min_dist;
}