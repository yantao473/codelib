#ifndef __LIST_H_
#define __LIST_H_

#define MAXSIZE 50

typedef int ElemType;

typedef struct{
    ElemType data[MAXSIZE];
    int length;
}SqList;

void initList(SqList *L);
void clearList(SqList L);
int listEmpty(SqList L);
int getElem(SqList L, int index, ElemType *e);
int locateElem(SqList L, ElemType e);
int listInsert(SqList *L, int index, ElemType e);
int listDelete(SqList *L, int index, ElemType e);
int listLength(SqList L);

#endif
