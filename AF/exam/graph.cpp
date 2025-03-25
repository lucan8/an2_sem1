// DON'T FORGET TO READ WITH CIN
#include <vector>
#include <memory>
#include <unordered_map>
#include <queue>
#include <stack>
#include <fstream>
#include <iostream>
#include <deque>
#include <climits>
using namespace std;


class Node;
class Edge;
class FlowEdge;

typedef shared_ptr<Node> shared_node;
typedef shared_ptr<Edge> shared_edge;
typedef shared_ptr<FlowEdge> shared_flow_edge;

enum EdgeType{
    FLOW,
    WEIGHTED,
    UNWEIGHTED
};

enum GraphType{
    DIRECTED,
    UNDIRECTED
};

// Forward edges point to residual edges and residual edges point to forwards edges
class Edge{
protected:
    shared_node start, end;
    int weight;
public:
    Edge(shared_node start, shared_node end, int weight = 0)
        : start(move(start)), end(move(end)), weight(weight){}
    
    shared_node getStart() const{return start;}
    shared_node getEnd() const{return end;}
    int getWeight() const{return weight;}
 
    void addWeight(int weight) {this->weight += weight;}
    void setWeight(int weight){this->weight = weight;}
    friend ostream& operator<<(ostream& out, const Edge& edge);
};

class FlowEdge: public Edge{
private:
    int capacity;
    shared_edge residual_edge;
public:
    FlowEdge(shared_node start, shared_node end, int weight = 0, int capacity = 0, shared_edge residual_edge = nullptr)
        : Edge(start, end, weight), capacity(capacity), residual_edge(residual_edge){}
    int getCapacity() const{return capacity;}

    int getRemainingFlow() const{return capacity - weight;}
    shared_edge getResidualEdge() const{ return residual_edge;}
    void setResidualEdge(shared_edge residual_edge){this->residual_edge = residual_edge;}

    bool isSaturated() const{return getRemainingFlow() == 0;}

    friend ostream& operator<<(ostream& out, const FlowEdge& edge);
};

class Node{
private:
    int val;
    vector<shared_edge> edges;
public:
    Node(int val): val(val){}

    int getVal() const{return val;}
    void addEdge(shared_edge edge){
        edges.push_back(move(edge));
    }

    void removeLastEdge(){
        this->edges.pop_back();
    }
    vector<shared_edge> getEdges(){return edges;}

    friend ostream& operator<<(ostream& out, const Node& node){
        out << node.val;
        return out;
    }
};

class BFSInfo{
private:
    shared_edge edge;
    bool visited;
public:
    BFSInfo(shared_edge edge = nullptr, bool visited = false)
        : edge(edge), visited(visited){}

    bool isVisited() const{return visited;}
    void visit(){visited = true;}
    void setEdge(shared_edge edge){this->edge = move(edge);}
    shared_edge getEdge() const{return edge;}
};
// TODO: Change to class
class DijkstraInfo{
private:
    int dist;
    shared_node pred;
    bool visited;
public:
    DijkstraInfo(int dist = 0, shared_node pred = nullptr, bool visited = false)
        : dist(dist), pred(pred), visited(visited){}
    
    bool isVisited() const{return visited;}
    int getDist() const{return dist;}
    shared_node getPred() const{return pred;}

    void visit(){this->visited = true;}
    void updateDistance(int new_dist, shared_node potential_pred){
        if (new_dist < this->dist){
            this->dist = new_dist;
            this->pred = move(potential_pred);
        }
    }
};


class PrimInfo{
private:
    vector<shared_edge> edges;
    int cost;
public:
    PrimInfo(): cost(0){}

    int getCost(){return cost;}
    const vector<shared_edge>& getEdges(){return edges;}

    void addEdge(shared_edge edge){
        cost += edge->getWeight();
        edges.push_back(move(edge));
    }
};

class Graph{
protected:
    vector<shared_node> nodes;
    vector<shared_edge> edges;

    GraphType g_type; //Directed/Undirected
    EdgeType e_type; //Weighted/Unweighted
public:
    Graph(size_t nr_nodes, size_t nr_edges, GraphType g_type, EdgeType e_type): g_type(g_type), e_type(e_type){
        this->nodes.reserve(nr_nodes + 1);
        // Reserve double the edges for undirected graphs
        if (g_type == GraphType::UNDIRECTED)
            this->edges.reserve(nr_edges * 2);
        else
            this->edges.reserve(nr_edges);
        // Ignoring the first element
        nodes.emplace_back(nullptr);

        // Creating and ading the nodes
        for (int i = 1; i <= nr_nodes; ++i)
            nodes.emplace_back(new Node(i));
    }

    shared_node getNodeByVal(int val){
        return this->nodes[val];
    }

    virtual void createFromEdgeList(istream& fin){
        // Reading until the edge vector is filled
        while (edges.capacity() != edges.size()){
            int start, end, weight = 0;
            fin >> start >> end;

            // Reading the weight if neccessary
            if (this->e_type == EdgeType::WEIGHTED)
                fin >> weight;
            
            shared_node &s_node = nodes[start], &e_node = nodes[end];

            // Create the edge
            shared_edge new_edge;
            new_edge = this->edges.emplace_back(new Edge(s_node, e_node, weight));

            // Add it to the start node
            s_node->addEdge(new_edge);

            // Create backwards edge for undirected graphs
            if (g_type == GraphType::UNDIRECTED){
                new_edge = this->edges.emplace_back(new Edge(e_node, s_node, weight));
                e_node->addEdge(new_edge);
            }
        }
    }

    // Returns all neccessary information for rebuilding any path from the start and all minimum distances
    unordered_map<shared_node, DijkstraInfo> dijkstra(size_t start_val) const{
        shared_node start;
        // Making sure the node is valid
        if (start_val < this->nodes.size())
            start = this->nodes[start_val];
        else
            throw runtime_error("Dijkstra: Node: " + to_string(start_val) + " is not in graph\n");

        // Keeping track of the information needed for this algorithm
        unordered_map<shared_node, DijkstraInfo> dij_map;
        for (auto n: this->nodes)
            if (n != nullptr)
                dij_map[n] = DijkstraInfo(INT_MAX, nullptr, false);
        
        //Visiting the start node
        dij_map[start].updateDistance(0, nullptr);
        dij_map[start].visit();

        // Compares nodes based on distance(used for min heap)
        auto dij_comp = [&dij_map](const shared_node& n1, const shared_node& n2){
            return dij_map.at(n1).getDist() > dij_map.at(n2).getDist();
        };
        
        // Create priority queue with custom comparator and push the start node
        priority_queue<shared_node, vector<shared_node>, decltype(dij_comp)> pq(dij_comp);
        pq.push(start);

        while (!pq.empty()){
            // Select the node with the smallest distance
            shared_node curr = pq.top();
            pq.pop();

            for (const auto& e: curr->getEdges()){
                shared_node neigbour = e->getEnd();

                //Visit neighbour
                if (!dij_map[neigbour].isVisited()){
                    dij_map[neigbour].visit();
                    pq.push(neigbour);
                }

                // Update distance and predecesor
                int new_dist = dij_map[curr].getDist() + e->getWeight();
                dij_map[neigbour].updateDistance(new_dist, curr);
            }
        }
        return dij_map;
    }

    PrimInfo prim(shared_node start = nullptr) const{
        // Select the first node if none is given
        if (start == nullptr)
            start = this->nodes[1];

        // Initialize visited map
        unordered_map<shared_node, bool> visited;
        for (auto n: this->nodes)
            if (n != nullptr)
                visited[n] = false;
        
        // Compares edges weights(min heap)
        auto prim_comp = [](const shared_edge& e1, const shared_edge& e2){
            return e1->getWeight() > e2->getWeight();
        };

        //Constructing the priority queue with the edges from the start and visiting the start
        priority_queue<shared_edge, vector<shared_edge>, decltype(prim_comp)> pq(prim_comp, start->getEdges());
        visited[start] = true;

        // Keeping track of the edges chosen and their cost
        PrimInfo result;

        while (!pq.empty()){
            // Removing edges until we get one that points to an unvisited node
            shared_edge curr_edge = pq.top();
            shared_node curr_node = curr_edge->getEnd();

            while (visited[curr_node] && !pq.empty()){
                pq.pop();
                curr_edge = pq.top();
                curr_node = curr_edge->getEnd();
            }
            // If pq is empty we stop
            if (pq.empty())
                break;

            // Remove the edge from the pq
            pq.pop();

            //Adding the chosen edge to the result and visiting the current node
            result.addEdge(curr_edge);
            visited[curr_node] = true;

            // Go through all the edges of this node and visit neigbours
            for (const auto& e: curr_node->getEdges()){
                // Pushing edges that point to unvisited neighbours
                shared_node neighbour = e->getEnd();
                if (!visited[neighbour])
                    pq.push(e);
            }
        }
        return result;
    }

    // Runs bfs on the node that has the value start_val
    unordered_map<shared_node, BFSInfo> bfs(int start_val, bool edmonds = false){
        shared_node start;
        // Check that the node value is valid and get the associated node
        if (start_val <= this->nodes.size())
            start = this->nodes[start_val];
        else
            throw runtime_error("bfs: Node: " + to_string(start_val) + " is not in graph\n");
        
        return bfs(start, edmonds);

    }
    // Returns bottleneck for edmonds
    unordered_map<shared_node, BFSInfo> bfs(const shared_node& start, bool edmonds = false){
        // Initialize BFS map
        unordered_map<shared_node, BFSInfo> bfs_map;
        for (const auto& n: nodes)
            if (n != nullptr)
                bfs_map[n];
        
        // Visit start node and add to queue
        bfs_map[start].visit();
        queue<shared_node> q;
        q.push(start);

        while (!q.empty()){
            shared_node curr = q.front();

            // Visit neighbours of current node, push them to queue and store their predecesor
            for (const auto& e: curr->getEdges()){
                shared_node neighbour = e->getEnd();

                // If used by edmonds algo, ignore the saturated edges
                if (edmonds && static_pointer_cast<FlowEdge>(e)->isSaturated())
                    continue;

                // Visit unvisited neighbour
                if (!bfs_map[neighbour].isVisited()){
                    bfs_map[neighbour].visit();
                    bfs_map[neighbour].setEdge(e);
                    q.push(neighbour);
                }
            }
            q.pop();
        }
        return bfs_map;
    }

    deque<shared_node> topoSort(){
        // Initialize visited map
        unordered_map<shared_node, bool> visited;
        for (auto n: this->nodes)
            if (n != nullptr)
                visited[n] = false;

        // Get starting nodes
        vector<shared_node> start_nodes = this->getTopoStartNodes();
        deque<shared_node> sorted_nodes;

        // Run DFS on each of the start nodes
        for (const auto& n: start_nodes)
            this->DFS(n, visited, sorted_nodes);
        
        return sorted_nodes;
    }
    
    void DFSIterative(const shared_node& start, unordered_map<shared_node, bool>& visited, deque<shared_node>& res){
        
    }
    // TODO: Not recursive
    void DFS(const shared_node& start, unordered_map<shared_node, bool>& visited, deque<shared_node>& res){
        visited[start] = true;

        for (const auto& e: start->getEdges()){
            shared_node neighbour = e->getEnd();
            if (!visited[neighbour])
                DFS(neighbour, visited, res);
        }
        res.push_front(start);
    }

    vector<shared_node> getTopoStartNodes(){
        unordered_map<shared_node, int> inner_degree_map = this->getInnerDegrees();

        vector<shared_node> result;
        result.reserve(this->nodes.size());

        //Keeping only the nodes that have inner degree 0
        for (const auto& p: inner_degree_map)
            if (!p.second)
                result.push_back(p.first);
        
        return result;
    }

    // Returns the inner degree of each node
    unordered_map<shared_node, int> getInnerDegrees(){
        // Initialize inner degree map
        unordered_map<shared_node, int> inner_degree_map;
        for (auto n: this->nodes)
            if (n != nullptr)
                inner_degree_map[n] = 0;
        
        // Getting the inner degree of each node
        for (const auto& e: this->edges)
            inner_degree_map[e->getEnd()]++;
        
        return inner_degree_map;
    }

    Graph getTranspose(){
        Graph res(nodes.size() - 1, edges.size(), g_type, e_type);
        // Swap direction of edges
        for (const auto& e: edges){
            shared_edge& edge = res.edges.emplace_back(new Edge(res.nodes[e->getEnd()->getVal()],
                                                                res.nodes[e->getStart()->getVal()],
                                                                e->getWeight()));
            res.nodes[e->getEnd()->getVal()]->addEdge(edge);
        }
        return res;
    }

    void removeEdge(int start_val, int end_val){
        shared_node start_node = this->nodes[start_val];
        shared_node end_node = this->nodes[end_val];

        // Remove neighbour from each node
        start_node->removeLastEdge();
        end_node->removeLastEdge();
        
        //Remove from edge list
        this->edges.pop_back();
        this->edges.pop_back();
    }

    //For Undirected graphs
    void addEdge(int start_val, int end_val){
        shared_node start_node = this->nodes[start_val];
        shared_node end_node = this->nodes[end_val];

        this->addEdge(start_node, end_node);
    }

    void addEdge(shared_node& start, shared_node& end){
        shared_edge new_edge = this->edges.emplace_back(new Edge(start, end));
        shared_edge back_edge = this->edges.emplace_back(new Edge(end, start));

        start->addEdge(new_edge);
        end->addEdge(back_edge);
    }

    int getMinimumDist(int end_node, unordered_map<shared_node, BFSInfo>& bfs_map){
        shared_node curr_node = this->nodes[end_node];
        shared_edge curr_edge = bfs_map.at(curr_node).getEdge();
        
        // Determine the minimum distance by going back on the path
        int min_dist = 0;
        while(curr_edge != nullptr){
            min_dist++;
            curr_node = curr_edge->getStart();
            curr_edge = bfs_map.at(curr_node).getEdge();
        }

        return min_dist;
    }

    vector<shared_node> getNodes(){return nodes;}
    const vector<shared_edge>& getEdges(){return edges;}
};

class FlowGraph: public Graph{
private:
    // Keeping track of the number of nodes on the left and right side(bipartite)
    size_t nr_nodes_left, nr_nodes_right;
    shared_node source, sink;
public:
    // Adding 2 to the number of nodes for the source and the sink
    // To the number of edges adding the ones coming from the source into the left nodes
    // and the ones coming from the right nodes to the sink
    FlowGraph(size_t nr_nodes_left, size_t nr_nodes_right, size_t nr_edges)
        : Graph(nr_nodes_left + nr_nodes_right + 2, nr_edges + nr_nodes_left + nr_nodes_right, GraphType::DIRECTED, EdgeType::FLOW),
          nr_nodes_left(nr_nodes_left), nr_nodes_right(nr_nodes_right){
        // Source is second last node, sink is last node
        source = nodes[nodes.size() - 2];
        sink = nodes.back();
    }

    // FlowGraph(size_t nr_nodes_left, ifstream& fin): Graph(nr_nodes_left, 0, GraphType::DIRECTED, EdgeType::FLOW),
    //     nr_nodes_left(nr_nodes_left), nr_nodes_right(0){
        
    //     vector<vector<int>> adiacency_list(nr_nodes_left + 1);
    //     int nr_nodes_right = 0;
        
    //     for (int i = 1; i <= nr_nodes_left; ++i){
    //         int nr_neighbours, neighbour;
    //         fin >> nr_neighbours;

    //         adiacency_list[i] = vector<int>(nr_neighbours + 1);
    //         for (int j = 1; j <= nr_neighbours; ++j){
    //             fin >> adiacency_list[i][j];
    //             nr_nodes_right = min(adiacency_list[i][j], nr_nodes_right);
    //         }
    //     }

    //     this->nr_nodes_right = nr_nodes_right;

    //     for (int s_node = 1; s_node <= nr_nodes_left; ++s_node)
    //         for (auto end_node: adiacency_list[s_node]){
    //             int new_end_node = end_node + nr_nodes_right;
    //         }
    // }

    void createFromEdgeList(istream& fin) override{
        // Add edges from source to left nodes
        for (int i = 1; i <= nr_nodes_left; ++i){
            int capacity;
            fin >> capacity;
            this->addEdge(source, this->nodes[i], capacity);
        }

        // Add edges from right nodes to sink
        for (int i = nr_nodes_left + 1; i <= nr_nodes_left + nr_nodes_right; ++i){
            int capacity;
            fin >> capacity;
            this->addEdge(this->nodes[i], sink, capacity);
        }

        // Reading until the edge vector is filled
        while (edges.capacity() != edges.size()){
            int start, end, capacity;
            fin >> start >> end >> capacity;
            
            shared_node &s_node = nodes[start], &e_node = nodes[end];
            this->addEdge(s_node, e_node, capacity);
        }
    }

    
    // This actually changes the edges weights(flow)
    void edmonds(){  
        while (true){
            // Value mostly keeps track of the edge used to get to the key
            unordered_map<shared_node, BFSInfo> bfs_map = bfs(source, true);

            // Calculate the bottleneck value
            int bottle_neck = getBottleNeck(sink, bfs_map);

            // No augmenting path was found
            if (bottle_neck == INT_MAX)
                break;

            // Add it to the edges used in the chosen augmenting path
            setBottleNeck(sink, bfs_map, bottle_neck);
        }
    }

    int getBottleNeck(const shared_node& sink, const unordered_map<shared_node, BFSInfo>& bfs_map){
        shared_node curr_node = sink;
        shared_edge curr_edge = bfs_map.at(curr_node).getEdge();
        int bottle_neck = INT_MAX;

        // Reconstructing the augmenting path and determining the bottleneck
        while(curr_edge != nullptr){
            bottle_neck = min(static_pointer_cast<FlowEdge>(curr_edge)->getRemainingFlow(), bottle_neck);
            curr_node = curr_edge->getStart();
            curr_edge = bfs_map.at(curr_node).getEdge();
        }

        return bottle_neck;
    }

    // Adds the bottleneck value to all the edges used in the augmenting path
    void setBottleNeck(const shared_node& sink, unordered_map<shared_node, BFSInfo>& bfs_map, int bottle_neck){
        shared_node curr_node = sink;
        shared_edge curr_edge = bfs_map.at(curr_node).getEdge();

        // Reconstructing the augmenting path and setting the bottle neck
        while(curr_edge != nullptr){
            // Add weight to the edge used
            curr_edge->addWeight(bottle_neck);

            // Remove weight from the opposite edge
            static_pointer_cast<FlowEdge>(curr_edge)->getResidualEdge()->addWeight(-bottle_neck);

            // Going to the node that got us here
            curr_node = curr_edge->getStart();

            curr_edge = bfs_map.at(curr_node).getEdge();
        }
    }

    void addEdge(shared_node& start, shared_node& end, int capacity){
        shared_edge new_edge = this->edges.emplace_back(new FlowEdge(start, end, 0, capacity));
        shared_edge residual_edge = this->edges.emplace_back(new FlowEdge(end, start, 0, 0, new_edge));

        static_pointer_cast<FlowEdge>(new_edge)->setResidualEdge(residual_edge);

        start->addEdge(new_edge);
        end->addEdge(residual_edge);
    }
};

void p1(istream& fin){
    int nr_nodes, nr_edges;
    fin >> nr_nodes >> nr_edges;

    Graph g1(nr_nodes, nr_edges, GraphType::UNDIRECTED, EdgeType::UNWEIGHTED);
    g1.createFromEdgeList(fin);

    // Get the initial minimum distance
    unordered_map<shared_node, BFSInfo> bfs_map = g1.bfs(1);
    int min_dist = g1.getMinimumDist(nr_nodes, bfs_map);

    int nr_queries;
    fin >> nr_queries;
    // For each query add the edge, run bfs and determine the minimum distance
    for (int i = 0; i < nr_queries; ++i){
        int start, end;
        fin >> start >> end;

        g1.addEdge(start, end);

        // Get new minimum distance
        unordered_map<shared_node, BFSInfo> bfs_map = g1.bfs(1);
        int new_min_dist = g1.getMinimumDist(nr_nodes, bfs_map);

        // Check if the the new minimum distance is < the initial
        if (new_min_dist < min_dist)
            cout << 1;
        else 
            cout << 0;
        g1.removeEdge(start, end);
    }
}

// Run BFS on each node to determine the minimum distance in the unweighted graph,
// for those that have distance < k add a teleportation edge
// Run dijkstra to get the minimum distance between the start and n
void p2(istream& fin){
    int nr_nodes, nr_edges, telportation_sec, minimum_nodes;
    fin >> nr_nodes >> nr_edges >> telportation_sec >> minimum_nodes;

    Graph g1(nr_nodes, nr_edges, GraphType::UNDIRECTED, EdgeType::WEIGHTED);
    g1.createFromEdgeList(fin);

    // Get the initial minimum distance
    for (const auto& n: g1.getNodes()){
        unordered_map<shared_node, BFSInfo> bfs_map = g1.bfs(n);
        // TODO: ADD TELEPORTATION NODES
    }

    // Get distance in weighted graph from 1 to n
    unordered_map<shared_node, DijkstraInfo> dijkstra_map = g1.dijkstra(1);
    cout << dijkstra_map[g1.getNodeByVal(nr_nodes)].getDist();
}

//Construct the flow graph, adding 2 more nodes, the source and the sink
// From the source go to each of the "land" nodes with and edge of capacity 1
// From the land nodes to their sent "oracle" nodes with edges of capacity 1
// From each oracle node to sink with an edge of capacity 1
// Run edmonds-karp and after it ends check if all the edges from the "oracles" to the sink are saturated 
void p3(istream& fin){

}

int main(){
    // ifstream fin("graph.in");
    // ofstream fout("graph.out");

    // if (!fin){
    //     cerr << "Could not open file\n";
    //     return -1;
    // }

    //p1(cin);
    p2(cin);
    //cout << "Done\n";
}

ostream& operator<<(ostream& out, const FlowEdge& edge){
    out << *edge.start << " " << *edge.end << " " << edge.weight << " " << edge.capacity;
    return out;
}
ostream& operator<<(ostream& out, const Edge& edge){
    out << *edge.start<< " " << *edge.end<< " " << edge.weight;
    return out;
}