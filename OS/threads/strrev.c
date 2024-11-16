#include <stdlib.h>
#include <pthread.h>
#include <string.h>
#include <stdio.h>

void* myStrrev(void* string){
    char* str = (char*)string;
    size_t s_len = strlen(str);
    char* res = malloc(sizeof(char) * (s_len + 1));

    if (res == NULL)
        return NULL;

    for (int i = 0; i < s_len; ++i)
        res[i] = str[s_len - i - 1];

    res[s_len] = '\0';
    return res;
}

int main(int argc, char* argv[]){
    if (argc != 2){
        printf("Input format: ./strrev string\n");
        return -1;
    }

    char* string = argv[1];

    pthread_t thr;
    if (pthread_create(&thr, NULL, myStrrev, string) != 0){
        perror(NULL);
        return -1;
    }

    char* rev_string;
    if (pthread_join(thr, (void**)&rev_string) !=0){
        perror(NULL);
        return -1;
    }

    if (rev_string == NULL){
        perror(NULL);
        return -1;
    }

    printf("%s\n", rev_string);
    free(rev_string);
}