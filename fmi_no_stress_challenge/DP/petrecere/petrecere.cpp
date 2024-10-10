#include <iostream>
#include <vector>
#include <numeric>
#include <algorithm>
#include <fstream>
#include <ranges>
#include <stdint.h>
using namespace std;

//TO DO: Implements division
struct MyVector{
    int* container;
    uint32_t size, capacity;
    MyVector(int capacity): capacity(capacity), size(0){
        container = new int[capacity];
        for (int i = 0; i < capacity; ++i)
            container[i] = 0;
    }
    int& operator[](uint32_t index){
        if (index >= capacity)
            throw runtime_error("MyVector capacity exceeded");
        if (index >= size)
            size = index + 1;
        return container[index];
        
    }
    void shrink(uint32_t n){
        if (n >= size)
            size = 0;
        else
            size -= n;
    }
};
struct NumarMare{
    const static int base = 10;
    const static int max_segments_count = 3000;
    bool minus = false;
    MyVector segments;

    //val <= 0-9
    NumarMare(int val = 1): segments(max_segments_count){segments[0] = val;}
    NumarMare(const MyVector& v): segments(v){}

    NumarMare operator+=(const NumarMare& other);
    NumarMare operator-=(const NumarMare& other);
    NumarMare operator*(const NumarMare& other) const;
    NumarMare operator/(const NumarMare& other) const;
    NumarMare operator-()const;
    static NumarMare _minus(const NumarMare& abs_bigger, const NumarMare& abs_smaller){
        NumarMare res = abs_bigger;
        bool carry = false;

        for (int i = 0; i < abs_bigger.segments.size; ++i){
            res.segments[i] = (res.segments[i] - abs_smaller.segments[i] - carry + base) % base;
            carry = abs_bigger.segments[i] > abs_smaller.segments[i]; 
        }



    }

    NumarMare operator+(const NumarMare& other) const;
    NumarMare operator<<(int n);
    bool operator<(const NumarMare& other) const;
    int compAbs(const NumarMare& other) const;
    //other <= 1000
    NumarMare operator*(int other) const;
    friend ostream& operator<<(ostream& out, const NumarMare& big_nr);

};

ifstream fin("petrecere.in");
ofstream fout("petrecere.out");

const int MAX_N = 1001;
vector<NumarMare> factorials(MAX_N, 1);

//Exists only for n even(when 0 pairs are fixed)
vector<NumarMare> getUnstaticCombinations(int n);
//combines nr_values in nr_ways(combine(4,2) = 6, combine(5, 2) = 10...)
NumarMare combine(int nr_values, int nr_ways);
//fills global varible factorials
void fillFactorials(int n);


int main(){
    int n;
    fin >> n;
    fout << getNrCombinations(n);
}



NumarMare getNrCombinations(int n){
    vector<NumarMare> nr_comb(n + 1, 0);
    nr_comb[3] = 3;

    //If n is even 0 pairs are fixed
    vector<NumarMare> nr_unstatic_comb = getUnstaticCombinations(n);

    for (int i = 4; i <= n; ++i){
        //First we calculate how many static combinations we have, where at least one pair is fixed(ex: (1,1))
        for (int j = i - 1; j >= 2; --j)
            //Using the principle of inclusion and exclusion
            if ((i - j) % 2)
                nr_comb[i] += combine(i, j) * nr_comb[j];
            else
                nr_comb[i] -= combine(i, j) * nr_comb[j];

        //Then we add the combinations in which there are no fixed points(for even i's)
        nr_comb[i] += nr_unstatic_comb[i] * (i % 2);
    }
    return nr_comb[n];
}


vector<NumarMare> getUnstaticCombinations(int n){
    vector<NumarMare> nr_unstatic_combinations(n / 2 + 1, 1);
    for (int i = 4; i <= n; i+= 2)
        nr_unstatic_combinations[i / 2] = nr_unstatic_combinations[i / 2 - 1] * (i - 1);
    return nr_unstatic_combinations;
}

NumarMare combine(int nr_values, int nr_ways){
    return factorials[nr_values] / (factorials[nr_values - nr_ways] * factorials[nr_ways]);
}

void fillFactorials(int n){
    for (int i = 1; i <= n; ++i)
        factorials[i] = factorials[i - 1] * i;
}


NumarMare NumarMare :: operator+=(const NumarMare& other){
    bool carry = false;
    if (other.minus == this->minus){
        for (int i = 0; i < max(segments.size, other.segments.size); ++i){
            int other_seg;
            //Setting the current other seg to 0 if out of bounds
            if (i >= other.segments.size)
                other_seg = 0;
            else{
                //Add another empty segment until we match the legth of the other
                if (i >= segments.size)
                    segments.push_back(0);
                other_seg = other.segments[i];
            }

            segments[i] += other_seg + carry;
            carry = segments[i] / base;
            segments[i] %= base;
        }
        if (carry)
            segments.push_back(1);
    }
    else

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


NumarMare NumarMare :: operator*(const NumarMare& other) const{
    NumarMare res;
    for (int i = 0; i < this->segments.size; ++i)
        res += (other * segments[i]) << i;
    return res;
}

NumarMare NumarMare :: operator/(const NumarMare& other) const{

}


NumarMare NumarMare :: operator-=(const NumarMare& other){
    for (int i )
}

NumarMare NumarMare :: operator+(const NumarMare& other) const{
    if (this->minus != other.minus){
        bool comp_abs = this->compAbs(other);
        if (comp_abs == 0)
            return 0;
        //abs(this) < abs(other)
        else if (comp_abs == 1){

        }
        //abs(this) > abs(other);    
        else{

        }


    }

}


NumarMare NumarMare :: operator<<(int n){
    vector<int> shift_v(n, 0);
    NumarMare cpy = *this;
    cpy.segments.insert(cpy.segments.begin(), shift_v.begin(), shift_v.end());
    return cpy;
}


bool NumarMare :: operator<(const NumarMare& other) const{
    if (this->minus != other.minus)
        return this->minus && !other.minus;
    
    int comp_abs = this->compAbs(other);
    if (comp_abs == 0)
        return false;
    
    //0 is now this > other and 2 is now this < other
    comp_abs++;

    return ((bool)comp_abs && !minus) || (!(bool)comp_abs && minus);
}


int NumarMare :: compAbs(const NumarMare& other) const{
    if (segments.size < other.segments.size)
        return 1;
    if (segments.size > other.segments.size)
        return -1;

     for (const auto& [this_seg, other_seg] : std :: views :: zip(segments, other.segments)){
        if (this_seg < other_seg)
            return 1;
        if (this_seg > other_seg)
            return -1;   
     }
    return 0;
}


 NumarMare NumarMare :: operator-() const{
    NumarMare cpy = *this;
    cpy.minus = !cpy.minus;
    return cpy;
}
/*
void getCombinations2(vector<int> v, vector<vector<int>>& comb){
    for (int i = 1; i < v.size() - 1; ++i)
        if (i == v[i])
            for (int j = i + 1; j < v.size(); ++j)
                if (j == v[j]){
                    vector<int> cpy = v;
                    swap(cpy[i], cpy[j]);
                    //If the combination is different to add it to the set
                    if (find(comb.begin(), comb.end(), cpy) == comb.end()){
                        comb.push_back(cpy);
                        getCombinations2(cpy, comb);
                    }
                }
}*/