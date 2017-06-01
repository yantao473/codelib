#include <stdlib.h>
#include "list.h"

void initList(SqList *L) {
    int i;
    for(i = 0; i < MAXSIZE; i++) {
        L->data[i] = 0;
    }

    L->length = 0;
}

void clearList(SqList L) {
    int i;
    for(i = 0; i < L.length; i++) {
        L.data[i] = 0;
    }

    L.length = 0;
}

int listEmpty(SqList L) {
    return  L.length;
}

int getElem(SqList L, int index, ElemType *e) {
    if(L.length == 0 || index > MAXSIZE) {
        return -1;
    }

    int i;
    for(i = 0; i < L.length; i++) {
        if(i == index) {
            *e = L.data[i];
            break;
        }
    }
    return 0;
}

