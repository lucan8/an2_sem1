#include <stdlib.h>
#include <pthread.h>
#include <semaphore.h>
#include <stdio.h>
#include <stdbool.h>

#define NR_THREADS 5

pthread_mutex_t mutex;
//Only used for barrierPoint1()
sem_t semaphore;

//For barrierPoint()
struct Barrier{
    unsigned int nr_waiting;
    unsigned int max_nr_waiting;
    bool down;
    
} barrier;

//For barrierPoint1()
bool barrier_down = true;

void barrierPoint1();

void initBarier(unsigned int max_nr_waiting);
void barrierPoint();
void* runner_func(void* args);

int main(){
    if (pthread_mutex_init(&mutex, NULL) != 0){
        perror(NULL);
        return -1;
    }

    if (sem_init(&semaphore, 0, NR_THREADS) != 0){
        perror(NULL);
        return -1;
    }

    // Allocating memory for the threads
    pthread_t* threads = malloc(NR_THREADS * sizeof(pthread_t));
    if (threads == NULL){
        perror(NULL);
        return -1;
    }

    //initBarier(NR_THREADS);
    for (int i = 0; i < NR_THREADS; ++i){    
        //Creating the threads to execute runner
        if (pthread_create(&threads[i], NULL, runner_func, NULL) != 0){
            perror(NULL);
            threads[i] = 0;
        }
    }

    for (int i = 0; i < NR_THREADS; ++i)
        if (threads[i] != 0){
            int* error;
            if (pthread_join(threads[i], (void**)&error) != 0)
                perror(NULL);
            else if (error != NULL)
                printf("Thread %ld failed\n", threads[i]);
        }
    
    if (pthread_mutex_destroy(&mutex) != 0){
        perror(NULL);
        return -1;
    }

    if (sem_destroy(&semaphore) != 0){
        perror(NULL);
        return -1;
    }

    free(threads);
    return 0;
}

void initBarier(unsigned int max_nr_waiting){
    barrier.nr_waiting = 0;
    barrier.max_nr_waiting = max_nr_waiting;
    barrier.down = true;
}

void barrierPoint(){
    //Reaching the barrier
    pthread_mutex_lock(&mutex);
    barrier.nr_waiting++;
    pthread_mutex_unlock(&mutex);

    //Busy waiting until al threads arrive while the barrier is set down
    while (barrier.nr_waiting < barrier.max_nr_waiting && barrier.down);

    pthread_mutex_lock(&mutex);

    //Lifting the barrier
    if (barrier.down == true){
        printf("    Barrier Lifted!\n");
        barrier.down = false;
    }

    //Passing the barrier
    barrier.nr_waiting--;

    //Last thread sets the barrier back down
    if (barrier.nr_waiting == 0)
        barrier.down = true;

    pthread_mutex_unlock(&mutex);
}

void barrierPoint1(){
    pthread_mutex_lock(&mutex);

    //Getting remaining threads needed to lift barrier
    unsigned int remaining;
    sem_getvalue(&semaphore, &remaining);

    printf("Remaining %d\n", remaining);
    sem_wait(&semaphore);

    pthread_mutex_unlock(&mutex);

    //Busy waiting for the rest of the threads
    while (remaining > 1 && barrier_down);
    
    pthread_mutex_lock(&mutex);

    //Lifting the barrier and passing the barrier
    barrier_down = false;
    sem_post(&semaphore);

    if (remaining == 0)
        barrier_down = true;
    
    pthread_mutex_unlock(&mutex);
}

void* runner_func(void* args){
    printf("Thread %ld reached the barrier!\n", pthread_self());
    barrierPoint1();
    printf("Thread %ld passed the barrier!\n", pthread_self());
    return NULL;
}
