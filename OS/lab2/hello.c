#include <unistd.h>
#include <stdio.h>
int main(){
    if (write(1, "hello_world\n", 12) < 0){
        perror(NULL);
        return -1;
    }
}