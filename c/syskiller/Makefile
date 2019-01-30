
all:syskiller 

syskiller:syskiller.c
	gcc -o syskiller syskiller.c

clean:
	rm syskiller

run:
	killall syskiller && nohup ./syskiller > /dev/null 2>&1 &

