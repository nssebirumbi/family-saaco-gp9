#include<stdio.h>
#include<stdlib.h>

#include<sys/socket.h>
#include<sys/types.h>
#include<netinet/in.h>
#include<arpa/inet.h>
#include<string.h>
int main(){
	int choose;
	char counter[1024];
	char username[1024];
	char message[1024];

	//prompting for the username from the client
	printf("Enter your username\n");
	gets(username);
	printf("Submit commands following the order below\n");
	printf("1: contribution amount reciept_number\n");
	printf("2: loan_request amount \n");
	printf("3: loan_repayment amount reciept number\n");
	printf("4: business_idea capital description\n");
	printf("5: contribution check \n");
	printf("6: benefits check \n");
	printf("7: loan repayment_details \n");
	printf("8: loan status \n\n");
	printf("Enter 1 to exit\n");
	printf("Enter your  command\n");
	if(username!=NULL){
	//creating a socket
	while(1){
	int net_socket = socket(AF_INET, SOCK_STREAM, 0);
	
	// adress of the socket
	struct sockaddr_in server_address;
	server_address.sin_family = AF_INET;
	server_address.sin_port = htons(9000);
	server_address.sin_addr.s_addr = INADDR_ANY;
	
	int conn_status = connect(net_socket,(struct sockaddr*) &server_address, sizeof(server_address));
	if(conn_status == -1){
	printf("\t\tERROR connection to the server failed!!!\n");
	}else{
		printf("\t\tConnection to the server established.....\n");
	}

	//getting client commands
	gets(message);
	if(strcmp(message,"1")==0){
		break;
	}else{
	strcat(message," ");
	strcat(message,username);
	}

	//sending data to the server		
	send(net_socket, message, sizeof(message), 0);

	//recieving from the server
	recv(net_socket, counter, sizeof(counter), 0);
	printf("\t\t-------------------------------\n");
	printf("\t\t%s\n",message);
	printf("\t\t%s\n",counter);
	printf("\t\t-------------------------------\n");
	close(net_socket);
	}
	}

	return 0;
}
