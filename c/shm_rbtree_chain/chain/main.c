#include <stdio.h>
#include <stdlib.h>
#include <stdint.h>

#define MAXSIZE 10

#pragma pack(4)
typedef struct {
    int blknum;
    int next;
} chainNode;

typedef struct {
    chainNode *baseaddr;
    int chainHead;
    int chainRear;
    void *rbroot;
} headnode;
#pragma pack()

// 如果有返回查找节点的前驱节点编号,否则返回 -1
int chain_search(chainNode *baseaddr, int chead, int crear, int blknum) {
    chainNode *p = baseaddr + chead;
    chainNode *r = baseaddr + crear;
    do {
        if(p->next == blknum) {
            return p->blknum;
        }
        p = baseaddr + p->next;
    } while(p->blknum != r->next) ;
    return -1;
}

// 应用场景不会删除head和rear节点
int chain_delete(chainNode *baseaddr, int chead, int crear, int blknum) {
    int preblknum = chain_search(baseaddr, chead, crear, blknum);
    printf("%d\n", preblknum);
    if( preblknum == -1) {
        return -1;
    } else {
        chainNode *p = baseaddr + preblknum;
        chainNode *q = baseaddr + blknum;
        p->next = q->next;

        //插入到头
        p = baseaddr + chead;
        q->next = p->next;
        p->next = q->blknum;
        return 0;
    }
}

int chain_get_avaiable_blknum(headnode *head, chainNode *baseaddr, int chead) {
    chainNode *phead = baseaddr + chead;
    head->chainHead = phead->next;
    head->chainRear= phead->blknum;
    return phead->blknum;
}

void print_chain(chainNode *baseaddr, int chead, int crear) {
    chainNode *p = baseaddr + chead;
    chainNode *r = baseaddr + crear;

    printf("head=%d,rear=%d\n\n", chead, crear);

    do {
        printf("num=%d, next=%d\n", p->blknum, p->next);
        p = baseaddr + p->next;
    } while(p->blknum != r->next) ;
}

int main() {
    headnode *head;
    head = (headnode*)malloc(sizeof(headnode) + MAXSIZE * sizeof(chainNode));

    if(NULL == head) {
        printf("malloc error\n");
    }

    head->baseaddr = (chainNode *)( head + sizeof(headnode));
    int i;
    int initlen = MAXSIZE;

    chainNode *cp;
    cp = head->baseaddr;

    for(i = 0; i < initlen; i++) {
        cp->blknum = i;
        cp->next = (i + 1) % initlen;
        head->chainRear = i;
        head->chainHead = cp->next;
        cp = head->baseaddr + cp->next;
    }

    chain_get_avaiable_blknum(head, head->baseaddr, head->chainHead);
    chain_get_avaiable_blknum(head, head->baseaddr, head->chainHead);
    chain_get_avaiable_blknum(head, head->baseaddr, head->chainHead);

    print_chain(head->baseaddr, head->chainHead, head->chainRear);

    free(head);
    return 0;
}
