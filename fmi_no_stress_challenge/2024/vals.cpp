#include <iostream>
#include <vector>
#include <algorithm>
using namespace std;

int main(){
    int n;
    cin >> n;

    vector<int> fete(n, 0), baieti(n, 0);
    for (int i = 0; i < n; ++i)
        cin >> fete[i];
    for (int j = 0; j < n; ++j)
        cin >> baieti[j];
    
    sort(fete.begin(), fete.end());
    sort(baieti.begin(), baieti.end());

    int i = 0, j = 0;
    while (j < n){
        if (fete[j] > baieti[i])
            i++, j++;
        else 
            while(j < n && fete[j] < baieti[i])
                j++;
    }
    cout << n - i;


}