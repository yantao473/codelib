#ifndef __XDICT_H_
#define __XDICT_H_

typedef struct _node{
	char *name;
	char *value;
	struct _node *next;
}node;

#define HASHSIZE 101
static node* hashtab[HASHSIZE];

char *m_strdup(char *o);

void inithashtab();
unsigned int hash(char *s);
node *lookup(char *n);
char *get(char *name);
int install(char *name, char *value);
void displaytable();
void cleanup();

#endif
