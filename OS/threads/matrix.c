#include <stdlib.h>
#include <pthread.h>
#include <string.h>
#include <stdio.h>

//used for int matrices and pthread_t matrices
struct matrix{
    int** mat;
    int nr_lines, nr_col;
};

struct calcMatrixArgs{
   int **m1, **m2, **m3;
   int line, column, k;
};

//Calc elem at line, col for the resulting matrix(m3) which is m1 * m2
void* calcMatrixELement(void* args);

//Print matrix to file
void printMatrix(FILE* fout, const struct matrix* m);
void printResults(FILE* matrix_out, const struct matrix* m1,
                  const struct matrix* m2, const struct matrix* m3);

//creates matrix and initializes it from file
struct matrix readMatrix(FILE* fin);

//Creates(and initializes with 0) matrix 
struct matrix createMatrix(int nr_lines, int nr_col);

void freeMatrix(const struct matrix* m);

int main(){
    FILE* matrix_in = fopen("matrix.in", "r");
    if (matrix_in == NULL){
        perror(NULL);
        return -1;
    }

    struct matrix m1 = readMatrix(matrix_in);
    if (m1.mat == NULL){    
        perror("Alloc for m1 failed");
        return -1;
    }

    struct matrix m2 = readMatrix(matrix_in);
    if (m2.mat == NULL){    
        perror("Alloc for m2 failed");
        return -1;
    }

    
    struct matrix m3 = createMatrix(m1.nr_lines, m2.nr_col);
    if (m3.mat == NULL){    
        perror("Alloc for m3 failed");
        return -1;
    }
    
    //Keeping track of all created threads
    //pthread_t threads[m3.nr_lines][m3.nr_col];
    pthread_t* threads = malloc(sizeof(pthread_t) * m3.nr_lines * m3.nr_col);
    size_t nr_threads = 0;
    if (threads == NULL){
        perror(NULL);
        return -1;
    }

    //m3 = m1 * m2, with m3[i][j] being calc by individual thread
    for (int i = 0; i < m3.nr_lines; ++i)
        for (int j = 0; j < m3.nr_col; ++j){
            //Initializing arguments for func
            //Using dynamically allocated args
            //Because local one might get changed before the thread finishes execution
            struct calcMatrixArgs* args = malloc(sizeof(struct calcMatrixArgs));
            *args = (struct calcMatrixArgs){.m1 = m1.mat, .m2 = m2.mat, .m3 = m3.mat,
                                            .line  = i, .column = j, .k = m1.nr_col};
            //Attemping to create thread for the calculation of current result elem
            //FUNCTION WILL FREE ARGS
            if (pthread_create(&threads[nr_threads++], NULL, calcMatrixELement, args)){
                    perror(NULL);
                    return -1;
                }
        }
    
    //Joining threads with parent
    for (int i = 0; i < m3.nr_lines * m3.nr_col; ++i)
            if (pthread_join(threads[i], NULL)){
                perror(NULL);
                return -1;
            }
    
    //Open file for printing the result matrix
    FILE* matrix_out = fopen("matrix.out", "w");
    if (matrix_out == NULL){
        perror(NULL);
        return -1;
    }

    printResults(matrix_out, &m1, &m2, &m3);
    //Freeing resources
    free(threads);
    freeMatrix(&m1), freeMatrix(&m2), freeMatrix(&m3);
    fclose(matrix_in), fclose(matrix_out);
}

//Calc elem at line, col for the resulting matrix(m3) which is m1 * m2
void* calcMatrixELement(void* args){
    //Retrieving the arguments
    struct calcMatrixArgs* m_args = args;
    int **m1 = m_args->m1, **m2 = m_args->m2, **m3 = m_args->m3;
    int line = m_args->line, column = m_args->column, k = m_args->k;

    printf("\n%ld: line = %d   column = %d\n\n", pthread_self(), line, column);
    for (int i = 0; i < k; ++i){
        int v1 = m1[line][i], v2 = m2[i][column];
        m3[line][column] += v1 * v2;
        printf("%ld: m1[%d][%d] = %d\n", pthread_self(), line, i, v1);
        printf("%ld: m2[%d][%d] = %d\n", pthread_self(), i, column, v2);
    }

    free(m_args);

    return 0;
}

void printMatrix(FILE* fout, const struct matrix* m){
    fprintf(fout, "%d %d\n", m->nr_lines, m->nr_col);
    for (int i = 0; i < m->nr_lines; ++i){
        for (int j = 0; j < m->nr_col; ++j)
            fprintf(fout, "%d ", m->mat[i][j]);
        fprintf(fout, "\n");
    }           
}

void printResults(FILE* matrix_out, const struct matrix* m1, const struct matrix* m2,
                  const struct matrix* m3){
    fprintf(matrix_out, "Matrix1:\n");
    printMatrix(matrix_out, m1);
    fprintf(matrix_out, "\n");

    fprintf(matrix_out, "Matrix2:\n");
    printMatrix(matrix_out, m2);
    fprintf(matrix_out, "\n");

    fprintf(matrix_out, "Result:\n");
    printMatrix(matrix_out, m3);
    fprintf(matrix_out, "\n");
}

struct matrix createMatrix(int nr_lines, int nr_col){
    struct matrix res = {NULL, nr_lines, nr_col};
    res.mat = malloc(sizeof(int*) * nr_lines);

    if (res.mat == NULL)
        return res;

    for (int i = 0; i < res.nr_lines; ++i){
        //Allocating memory for entire line
        res.mat[i] = malloc(sizeof(int) * nr_col);
        if (res.mat == NULL)
            return res;

        //Initialzing each elem with 0
        for (int j = 0; j < res.nr_col; ++j)
            res.mat[i][j] = 0;
    }
    return res;
}


struct matrix readMatrix(FILE* fin){
    int nr_lines, nr_col;
    fscanf(fin, "%d %d", &nr_lines, &nr_col);

    struct matrix res = createMatrix(nr_lines, nr_col);
    if (res.mat == NULL)
        return res;

    for (int i = 0; i < res.nr_lines; ++i)
        for (int j = 0; j < res.nr_col; ++j)
            fscanf(fin, "%d", &(res.mat[i][j]));
    
    return res;
}

void freeMatrix(const struct matrix* m){
    for (int i = 0; i < m->nr_lines; ++i)
        free(m->mat[i]);
    free(m->mat);
}