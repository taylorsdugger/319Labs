package main;

import java.io.BufferedInputStream;
import java.io.IOException;
import java.math.BigInteger;
import java.net.ServerSocket;
import java.net.Socket;
import java.util.Scanner;

public class ChatApplication {
	
	ServerSocket serverSocket = null;
	Socket clientSocket = null;
	
	public static void main(String[] args) {
	
		ChatApplication a = new ChatApplication();
	}

	public ChatApplication(){
		
		System.out.println("Enter your Name: (Type in your name, then press Enter)");
		
		Scanner in = new Scanner(System.in);
		String name = in.next();
		
		try {
			serverSocket = new ServerSocket(78); // provide MYSERVICE at port 4444
		} catch (IOException e) {
			System.out.println("Could not listen on port: 78");
			System.exit(-1);
		}	
		
		try {
			Socket socket = new Socket("localhost", 78);
			System.out.println("Waiting to connect!");
			clientSocket = serverSocket.accept();

			System.out.println(name + " was connected to server.");
			//Thread t = new Thread(new ClientHandler(clientSocket, name));
			//t.start();

		} catch (IOException e) {
			System.out.println("Accept failed: 78");
			System.exit(-1);
		}

		System.out.println("\n1. Send a text message to the server.\n2. Send an image file to the server.");
		
		int sel = in.nextInt();
		
		if(sel == 1){
			System.out.println("Write your text message here: (ending with ;)\n");
		
			in.useDelimiter(";");
			String message = in.next();
		
			byte[] mes = message.getBytes();
			byte[] encMes = null;
			byte key = (byte) 11110000;
			
			for(int i = 0; i < mes.length; i++){
				encMes[i] = (byte)(mes[i] ^ key);
			}
			
		}else if(sel == 2){
			
		}else{
			System.out.println("Please enter 1 or 2");
		}
		
		//Thread t = new MyThread("StartServer");
		
	}
	
}
