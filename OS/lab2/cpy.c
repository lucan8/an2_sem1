#include <unistd.h>
#include <fcntl.h>
#include <stdio.h>
#include <sys/stat.h>
#include <stdlib.h>
int main(int argc, char* argv[]){
    if (argc < 3){
        printf("Error invalid format, correct format is ./cpy [src] [dest]");
        return -1;
    }

    //Open src file in read mode with read permission
    int src_d = open(argv[1], O_RDONLY, S_IRUSR);
    if (src_d < 0){
        perror("Source file: ");
        return -1;
    }

    //Open dest file in write mode, creating the file if not exists
    //With read and write permission
    int dest_d = open(argv[2], O_WRONLY | O_CREAT | O_TRUNC, S_IRUSR | S_IWUSR);
    if (dest_d < 0){
        perror("Destination file: ");
        return -1;
    }

    struct stat temp;
    if (stat(argv[2], &temp) < 0){
        perror("Stat dest");
        return -1;
    }

    //Allocate memory for read buffer
    int block_size = 4096;
    char* buffer = malloc(block_size * sizeof(char));

    if (buffer == NULL){
        perror("Read buffer allocation:" );
        return -1;
    }

    int read_block_bytes;
    do{
        //Read block of data(or less for the last read)
        read_block_bytes = 0;
        int temp_read_bytes;
        do{ 
            temp_read_bytes = read(src_d, buffer + read_block_bytes,
                                    block_size - read_block_bytes);
            if (temp_read_bytes < 0){
                perror("Read failed");
                return -1;
            }
            read_block_bytes += temp_read_bytes;
        } while (temp_read_bytes != 0);

        //Write read block of data
        int write_block_bytes = 0;
        do{
            write_block_bytes += write(dest_d, buffer + write_block_bytes,
                                       read_block_bytes - write_block_bytes);
            if (write_block_bytes < 0){
                perror("Writing failed");
                return -1;
            }
        } while (write_block_bytes != read_block_bytes);
        
    } while (read_block_bytes == block_size);
    
    struct stat s_in, s_out;
    if (stat(argv[1], &s_in) < 0){
        perror("Stat src");
        return -1;
    }

    if (stat(argv[2], &s_out) < 0){
        perror("Stat dest");
        return -1;
    }

    printf("Src file nr_bytes:%ld\n", s_in.st_size);
    printf("Dest file nr_bytes:%ld\n", s_out.st_size);
    free(buffer);
    return 0;
}