#include <iostream>
#include <fstream>
#include <unordered_map>
#include <vector>
#include <cstdint>
#include <numeric>
#include <cmath>
#include <array>
using namespace std;

ifstream fin("perm3.in");
ofstream fout("perm3.out");

//Always use base 10 for simpler formating
struct NumarMare{
    const static int base = 10;
    const static int max_segments_count = 3000;
    vector<int> segments;

    //val <= 1000
    NumarMare(int val = 1){segments.reserve(max_segments_count); segments.push_back(val);}
    NumarMare(const vector<int>& v): segments(v){}

    NumarMare operator+=(const NumarMare& other);
    //other <= 1000
    NumarMare operator*(int other) const;
    friend ostream& operator<<(ostream& out, const NumarMare& big_nr);

};

void createTest();

//Make factorial levels(levels[0] = (n-1)!, levels[1] = (n-2)! ...)
void makeLevels(vector<NumarMare>& levels, int n);

//Removes nr from remaining and decrements the value for every element with key > nr
void remove(unordered_map<int, int>& remaining, int nr);

//key-set_val, value-index
void readSet(unordered_map<int, int>& rev_set, int n);
void readAndNormalizePermutation(vector<int>& permutation, unordered_map<int, int>& rev_set, int n);

NumarMare getPermutationIndex(vector<int>& permutation, unordered_map<int, int>& remaining,
                             const vector<NumarMare>& levels, int n);
int main(){
    //createTest();
    int n;
    fin >> n;

    //Remaining values in permuation
    unordered_map<int, int> remaining;
    for (int i = 1; i <= n; ++i)
        remaining[i] = i;

    //each level is (n-i)!
    vector<NumarMare> levels(n, 1);
    makeLevels(levels, n);

    //set_value, index
    unordered_map<int, int> rev_set;
    readSet(rev_set, n);
    
    //Reading the permutation, but normalizing it to be values from 1-n
    vector<int> permutation(n + 1, 0);
    readAndNormalizePermutation(permutation, rev_set, n);

    NumarMare res = getPermutationIndex(permutation, remaining, levels, n);
    fout << res;
}

void createTest(){
    ofstream fout1("perm3.in");
    fout1 << 1000 << '\n';

    for (int i = 1; i<= 999; ++i)
        fout1 << i << " ";
    fout1 << 1000 << '\n';
    for (int i = 1000; i >= 2; --i)
        fout1 << i << " ";
    fout1 << 1;
}


void makeLevels(vector<NumarMare>& levels, int n){
    for (int i = n - 2; i >= 1; i--)
        levels[i] = levels[i + 1] * (n - i);
}


void remove(unordered_map<int, int>& remaining, int nr){
    remaining.erase(nr);
    for (auto& p : remaining)
        if (p.first > nr)
            p.second--;
}


void readSet(unordered_map<int, int>& rev_set, int n){
    //set_value, permutation index
    for (int i = 1; i <= n; ++i){
        int value;
        fin >> value;
        rev_set[value] = i;
    }
}

void readAndNormalizePermutation(vector<int>& permutation, unordered_map<int, int>& rev_set, int n){
    for (int i = 1; i <= n; ++i){
        int set_val;
        fin >> set_val;
        permutation[i] = rev_set[set_val];
    }
}

NumarMare getPermutationIndex(vector<int>& permutation, unordered_map<int, int>& remaining,
                         const vector<NumarMare>& levels, int n){
    NumarMare perm_index;
    for (int i = 1; i < n; ++i){
        int v = permutation[i];
        perm_index += levels[i] * (remaining[v] - 1);
        remove(remaining, v);
    }
    return perm_index;
}


NumarMare NumarMare :: operator+=(const NumarMare& other){
    bool carry = false;
    for (int i = 0; i < max(segments.size(), other.segments.size()); ++i){
        int other_seg;
        //Setting the current other seg to 0 if out of bounds
        if (i >= other.segments.size())
            other_seg = 0;
        else{
            //Add another empty segment until we match the legth of the other
            if (i >= segments.size())
                segments.push_back(0);
            other_seg = other.segments[i];
        }

        segments[i] += other_seg + carry;
        carry = segments[i] / base;
        segments[i] %= base;
    }
    if (carry)
        segments.push_back(1);
    return *this;
}


//other <= 1000
NumarMare NumarMare :: operator*(int other) const{
    NumarMare cpy = *this;
    int carry = 0;

    for (auto& seg : cpy.segments){
        seg = seg * other + carry;
        carry = seg / base;
        seg %= base;
    }
    while (carry){
        cpy.segments.push_back(carry % base);
        carry /= base;
    }
    return cpy;
}

ostream& operator<<(ostream& out, const NumarMare& big_nr){
    string output, expected("402387260077093773543702433923003985719374864210714632543799910429938512398629020592044208486969404800479988610197196058631666872994808558901323829669944590997424504087073759918823627727188732519779505950995276120874975462497043601418278094646496291056393887437886487337119181045825783647849977012476632889835955735432513185323958463075557409114262417474349347553428646576611667797396668820291207379143853719588249808126867838374559731746136085379534524221586593201928090878297308431392844403281231558611036976801357304216168747609675871348312025478589320767169132448426236131412508780208000261683151027341827977704784635868170164365024153691398281264810213092761244896359928705114964975419909342221566832572080821333186116811553615836546984046708975602900950537616475847728421889679646244945160765353408198901385442487984959953319101723355556602139450399736280750137837615307127761926849034352625200015888535147331611702103968175921510907788019393178114194545257223865541461062892187960223838971476088506276862967146674697562911234082439208160153780889893964518263243671616762179168909779911903754031274622289988005195444414282012187361745992642956581746628302955570299024324153181617210465832036786906117260158783520751516284225540265170483304226143974286933061690897968482590125458327168226458066526769958652682272807075781391858178889652208164348344825993266043367660176999612831860788386150279465955131156552036093988180612138558600301435694527224206344631797460594682573103790084024432438465657245014402821885252470935190620929023136493273497565513958720559654228749774011413346962715422845862377387538230483865688976461927383814900140767310446640259899490222221765904339901886018566526485061799702356193897017860040811889729918311021171229845901641921068884387121855646124960798722908519296819372388642614839657382291123125024186649353143970137428531926649875337218940694281434118520158014123344828015051399694290153483077644569099073152433278288269864602789864321139083506217095002597389863554277196742822248757586765752344220207573630569498825087968928162753848863396909959826280956121450994871701244516461260379029309120889086942028510640182154399457156805941872748998094254742173582401063677404595741785160829230135358081840096996372524230560855903700624271243416909004153690105933983835777939410970027753472000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000");
    for (int i = big_nr.segments.size() - 1; i >= 0; --i){
        out << big_nr.segments[i];
        output += to_string(big_nr.segments[i]);
    }
    if (output == expected)
        cout << "NAH, I'd win" << '\n';
    return out;
}