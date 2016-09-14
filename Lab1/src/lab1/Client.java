package lab1;

import java.io.BufferedOutputStream;
import java.io.IOException;
import java.io.PrintWriter;
import java.net.Socket;
import java.net.UnknownHostException;
import java.util.Scanner;

public class Client implements Runnable {
	
	Socket serverSocket;
	String serverHostName = "localhost";
	int serverPortNumber = 4444;
	String name = "";
	ServerListener s;
	
	public static void main(String[] args) {		
		new Thread(new Client()).start();
	}
	
	public void run() {
		System.out.println("Enter your Name: (Type in your name, then press Enter)");
		
		Scanner in = new Scanner(System.in);
		name = in.next();
		
		try {
			serverSocket = new Socket(serverHostName, serverPortNumber);
			
		} catch (UnknownHostException e) {
			e.printStackTrace();
		} catch (IOException e) {
			e.printStackTrace();
		}
		
		PrintWriter out;
		
		try {
			out = new PrintWriter(new BufferedOutputStream(serverSocket.getOutputStream()));
			out.println(name);
			out.flush();
			
		} catch (IOException e) {
			e.printStackTrace();
		}
		
		s = new ServerListener(this, serverSocket);
		new Thread(s).start();
		
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
	}

	public void handleMessage(String cmd, String s) {
		switch (cmd) {
		case "print":
			System.out.println(s);
			break;
		case "exit":
			System.exit(-1);
			break;
			
		default:
			System.out.println("client side: unknown command received:" + cmd);
		}
	}
}
