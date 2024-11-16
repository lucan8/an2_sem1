#include <stdlib.h>
#include <pthread.h>
#include <string.h>
#include <stdio.h>
#include <time.h>
#define MAX_RESOURCES 5

pthread_mutex_t mutex;
int available_resources = MAX_RESOURCES;

void* runner_func(void* args);
int decrease_count(int count);
int increase_count(int count);


int main(){
    int nr_threads = 10;

    if (pthread_mutex_init(&mutex, NULL) != 0){
        perror(NULL);
        return -1;
    }

    // Allocating memory for the threads
    pthread_t* threads = malloc(nr_threads * sizeof(pthread_t));
    if (threads == NULL){
        perror(NULL);
        return -1;
    }
    // Allocating memory for the numbers used by the thread's function
    int* numbers = malloc(nr_threads * sizeof(pthread_t));
    if (numbers == NULL){
        perror(NULL);
        return -1;
    }

    srand(time(NULL));

    for (int i = 0; i < nr_threads; ++i){    
        numbers[i] = (rand() % MAX_RESOURCES) + 1;
        //Creating the threads to execute runner
        //If it fails we set the thread's value to 0 in the "threads" array
        if (pthread_create(&threads[i], NULL, runner_func, &numbers[i]) != 0){
            perror(NULL);
            threads[i] = 0;
        }
    }

    //Joining threads
    for (int i = 0; i < nr_threads; ++i)
        if (threads[i] != 0){
            int* error;
            if (pthread_join(threads[i], (void**)&error) != 0)
                perror(NULL);
            else if (error != 0)
                printf("Thread %ld failed\n", threads[i]);
        }
        
    if (pthread_mutex_destroy(&mutex) != 0){
        perror(NULL);
        return -1;
    }

    free(threads);
    free(numbers);

    return 0;
}

void* runner_func(void* args){
    int count = *(int*)args;

    // int* ret = malloc(sizeof(int));
    // if (ret == NULL){
    //     perror(NULL);
    //     return ;
    // }

    if (count > MAX_RESOURCES){
        printf("Thread %ld asked for %d resources but the maximum is %d\n",
               pthread_self(), count, MAX_RESOURCES);
        return -1;
    }
    decrease_count(count);
    increase_count(count);
    return 0;
}

int decrease_count(int count){
    pthread_mutex_lock(&mutex);
    
    //If not enough resources are available we unlock the mutex and wait for resources
    if (available_resources < count){
        printf("Thread %ld: Waiting for %d resources...\n", pthread_self(), count);
        pthread_mutex_unlock(&mutex);

        while (available_resources < count);
        pthread_mutex_lock(&mutex);
    }

    available_resources -= count;
    printf("Thread %ld: Got %d resources, remaining %d\n",
            pthread_self(), count, available_resources);

    pthread_mutex_unlock(&mutex);
    return 0;
}

int increase_count(int count){
    pthread_mutex_lock(&mutex);

    available_resources += count;
    printf("Thread %ld: Released %d resources, remaining %d\n",
            pthread_self(), count, available_resources);

    pthread_mutex_unlock(&mutex);

    return 0;
}