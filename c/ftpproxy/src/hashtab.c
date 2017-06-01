#include <string.h>
#include <stdio.h>
#include <stdlib.h>

#include "lib.h"
#include "hashtab.h"


void hashtab_init(){
    int i;
    for(i = 0; i < HASHSIZE; i++){
        hashtab[i] = NULL;
    }
}

unsigned int hashtab_hash(char *s){
    unsigned int h = 0;
    for(; *s; s++)
        h = *s + h * 31;
    return h % HASHSIZE;
}

node* hashtab_lookup(char *n){
    unsigned int hi = hashtab_hash(n);
    node* np = hashtab[hi];
    for(; np != NULL; np = np->next){
        if(!strcmp(np->name, n)){
            return np;
        }
    }

    return NULL;
}

char* hashtab_strdup(char *o){
    int l = strlen(o)+1;
    char *ns = (char*)malloc(l * sizeof(char));
    strcpy(ns, o);

    if(ns == NULL){
        return NULL;
    } else{
        return ns;
    }
}

char* hashtab_get(char* name){
    node* n = hashtab_lookup(name);
    if(n == NULL){
        return NULL;
    } else{
        return n->value;
    }
}

int hashtab_install(char* name,char* value){
    unsigned int hi;
    node* np;

    if((np = hashtab_lookup(name)) == NULL){
        hi = hashtab_hash(name);
        np = (node*)malloc(sizeof(node));
        if(np == NULL){
            return 0;
        }
        np->name = hashtab_strdup(name);

        if(np->name == NULL) {
            return 0;
        }
        np->next = hashtab[hi];
        hashtab[hi] = np;
    } else{
        free(np->value);
    }

    np->value = hashtab_strdup(value);
    if(np->value == NULL) {
        return 0;
    }

    return 1;
}

/* A pretty useless but good debugging function,
   which simply displays the hashtable in (key.value) pairs
   */
void hashtab_display(){
    int i;
    node *t;
    for(i = 0; i < HASHSIZE; i++){
        if(hashtab[i] != NULL){
            t = hashtab[i];
            for(; t != NULL;t = t->next){
                printf("(%s=>%s) ",t->name,t->value);
            }
        }
    }
    printf("\n");
}

void hashtab_cleanup(){
    int i;
    node *np,*t;
    for(i = 0; i < HASHSIZE; i++){
        if(hashtab[i] != NULL){
            np = hashtab[i];
            while(np!=NULL){
                t = np->next;
                free(np->name);
                free(np->value);
                free(np);
                np = t;
            }
        }
    }
}

void hashtab_parsekv(char *from, int delim)
{
    int	k = 0;
    char *p = from;

    char key[MAX_SIZE];
    char val[MAX_SIZE];

    memset(key, 0, MAX_SIZE);
    memset(val, 0, MAX_SIZE);

    while(p = skip_ws(p), *p != delim){
        key[k++] = *p++;
    }

    key[k] = 0;


    k = 0;
    //skip delimiter
    p++;
    while(p = skip_ws(p), *p){
        val[k++] = *p++;
    }
    val[k] = 0;

    hashtab_install(key, val);
}

void args2hashmap(char *args, int fdelim, int sdelim){
    char *p = args;
    char pattern[MAX_SIZE];

    hashtab_init();

    while (p = skip_ws(p), *get_quoted(&p, fdelim, pattern, sizeof(pattern)) != 0) {
        hashtab_parsekv(pattern, sdelim);
    }
    //displaytable();
    //cleanup();
}
