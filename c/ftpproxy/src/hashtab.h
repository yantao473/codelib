#ifndef __HASHTAB_H_
#define __HASHTAB_H_

#define HASHSIZE 101
#define MAX_SIZE 100

typedef struct _node{
	char *name;
	char *value;
	struct _node *next;
}node;

static node* hashtab[HASHSIZE];

char *hashtab_strdup(char *o);

void hashtab_init();
unsigned int hashtab_hash(char *s);
node *hashtab_lookup(char *n);
char *hashtab_get(char *name);
int hashtab_install(char *name, char *value);
void hashtab_displaytable();
void hashtab_cleanup();

void hashtab_parsekv(char *from, int delim);
void args2hashmap(char *args, int fdelim, int sdelim);
#endif
