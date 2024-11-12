#include <iostream>
#include <vector>
using namespace std;

int main(){
    int n, m;
    cin >> n >> m;
    vector<vector<int>> v(n, vector(m, 0));

    int vmin = 10000, vmax = 0;
    int imin = 0, imax = 0;

    for (int i = 0; i < n; ++i){
        int s = 0;
        for (int j = 0; j < m; ++j){
            cin >> v[i][j];
            s += v[i][j];
        }
        if (s > vmax){
            vmax = s;
            imax = i;
        }
        if (s < vmin){
            vmin = s;
            imin = i;
        }
    }

    cout << imin + 1 << " " << imax + 1;

}