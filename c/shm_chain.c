#include <stdio.h>
#include <stdlib.h>
#include <string.h>
#include <sys/shm.h>

#define CHAIN_LEN 8 * 1024

#pragma pack(4)
typedef struct {
    int data;
    int next;
} node;
#pragma pack()


void fillshm(int shmid) {
    int i;
    node *p, *head;
    head = (node*)shmat(shmid, NULL, 0);
    if (head == (node *)(-1)) {
        perror("shmat");
        exit(1);
    }

    printf("writing to shared memory\n");
    p = head;
    for(i = 0; i < CHAIN_LEN; i++) {
        p->data = i;
        p->next = (i + 1);
        p = head + p->next;
    }
}

void printshm(int shmid) {
    int i;
    node *head, *p;

    head = (node *) shmat(shmid, NULL, 0);
    if(head == (node *) (-1)) {
        perror("shmat");
        exit(1);
    }

    p = head;
    for(i = 0; i < CHAIN_LEN; i++) {
        printf( "%d\n", p->data);
        p = head + p->next;
    }
}

int main() {
    int shmid;
    key_t key = 0xABCD1234;

    // see if the memory exists and print it if so
    if ((shmid = shmget (key, 0, 0)) != -1) {
        printshm( shmid );
    } else {
        // didn't exist, so create it
        if((shmid = shmget (key, sizeof(node) * CHAIN_LEN, IPC_CREAT | 0600)) == -1 ) {
            perror("shmget");
            exit(1);
        }

        printf("shmid = %d\n", shmid);

        fillshm(shmid);
        printf("Run another instance of this app to read the memory... (press a key): " );
        getchar();

        // delete it
        if (shmctl (shmid, IPC_RMID, NULL) < 0 ) {
            perror("semctl");
            exit(1);
        }
    }

    return 0;
}
