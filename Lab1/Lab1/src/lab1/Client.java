package lab1;

import java.io.BufferedOutputStream;
import java.io.IOException;
import java.io.PrintWriter;
import java.net.Socket;
import java.net.UnknownHostException;
import java.nio.file.Files;
import java.nio.file.Path;
import java.nio.file.Paths;
import java.util.Arrays;
import java.util.Base64;
import java.util.Scanner;

public class Client implements Runnable {
	
	Socket serverSocket;
	String serverHostName = "localhost";
	int serverPortNumber = 4444;
	ServerListener sl;
	String name;
		
	public void run() {
		
		Scanner in = new Scanner(System.in);
		System.out.print("Enter your Name: ");
		name = in.nextLine();
		
		// 1. CONNECT TO THE SERVER
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
		} catch (IOException e1) {
			e1.printStackTrace();
		}
		
		// 2. SPAWN A LISTENER FOR THE SERVER. THIS WILL KEEP RUNNING
		// when a message is received, an appropriate method is called
		sl = new ServerListener(this, serverSocket);
		new Thread(sl).start();
		System.out.println("Welcome to my chat application:");
		
		byte b = 0;
		
		// set our byte bit by bit = 11110000
		b = (byte) (b | (1 << 7)); // 1
		b = (byte) (b | (1 << 6)); // 1
		b = (byte) (b | (1 << 5)); // 1
		b = (byte) (b | (1 << 4)); // 1
		b = (byte) (b & ~(1 << 3));// 0
		b = (byte) (b & ~(1 << 2));// 0
		b = (byte) (b & ~(1 << 1));// 0
		b = (byte) (b & ~(1 << 0));// 0
		
		if(!name.equals("admin")){
			System.out.println("1. Send a text message to the server");
			System.out.println("2. Send an image file to the server");
			System.out.println("3. Log out of chatroom");
			
			int command = 0;
			command = in.nextInt();
			
			while(command != 1 && command != 2 && command != 3){
				System.out.println("Invalid command, please enter a 1, 2 or 3");
				command = in.nextInt();
			}// end while loop taking command
			
			while(command != 3){
				if(command == 1){
					System.out.print("Enter Message: ");
					try {
						out = new PrintWriter(new BufferedOutputStream(serverSocket.getOutputStream()));
						in.nextLine();
						String message = "print " + in.nextLine();
						// encrypt the message
						message = Encryption.encode(message, b);
						out.println(message);
						out.flush();
						System.out.println("Message sent to server.");
						System.out.println();
					} catch (IOException e) {
						e.printStackTrace();
					}
					
				}else if(command == 2){
					System.out.print("Enter image name: ");
					
					try {
						out = new PrintWriter(new BufferedOutputStream(serverSocket.getOutputStream()));
						in.nextLine();
						String image_loc = in.nextLine();
						String message = "image " + get_file_encoded_string(image_loc);
						
						// encrypt the message
						message = Encryption.encode(message, b);
						out.println(message);
						out.flush();
						System.out.println("Image sent to server.");
						System.out.println();
					} catch (IOException e) {
						e.printStackTrace();
					}
				}
				System.out.println("1. Send a text message to the server");
				System.out.println("2. Send an image file to the server");
				System.out.println("3. Log out of chatroom");
				command = in.nextInt();
			}// end while loop taking input
			
			try {
				out = new PrintWriter(new BufferedOutputStream(serverSocket.getOutputStream()));			
				out.println(Encryption.encode("leave", b));
				out.flush();
				System.out.println("Successfully logged out.");
				System.exit(0);
			} catch (IOException e) {
				e.printStackTrace();
			}
		}else{
			System.out.println("1. Broadcast message to all clients");
			System.out.println("2. List messages so far (from chat.txt)");
			System.out.println("3. Delete a selected message from chat.txt - give a message number");
			
			int command = 0;
			command = in.nextInt();
			
			while(command != 1 && command != 2 && command != 3 && command != 4){
				System.out.println("Invalid command, please enter a 1, 2, 3 or 4");
				command = in.nextInt();
			}// end while loop taking command
			
			while(command != 4){
				
				if(command == 1){
					System.out.print("Enter Message: ");
					
					try {
						out = new PrintWriter(new BufferedOutputStream(serverSocket.getOutputStream()));
						in.nextLine();
						String message = "broadcast " + in.nextLine();
						// encrypt the message
						message = Encryption.encode(message, b);
						out.println(message);
						out.flush();
						System.out.println("Message broadcasted to all clients.");
						System.out.println();
					} catch (IOException e) {
						e.printStackTrace();
					}
					
				}
				System.out.println("1. Broadcast message to all clients");
				System.out.println("2. List messages so far (from chat.txt)");
				System.out.println("3. Delete a selected message from chat.txt - give a message number");
				command = in.nextInt();
			}// end while loop over admin commands
			
			
		}// end if user is admin
		
	}// end of run method
	
	public String get_file_encoded_string(String image_loc) throws IOException{
		
		Path path = Paths.get(image_loc);
		byte[] data = Files.readAllBytes(path);
		int i;
		String encoded_string = "";
				
		for(i = 0; i < data.length; i = i + 3){
			
			if(i < data.length && i+1 < data.length && i+2 < data.length){
				byte[] old_bytes = new byte[3];
				
				old_bytes[0] = data[i];
				old_bytes[1] = data[i+1];
				old_bytes[2] = data[i+2];
						
				data[i] = 0;
				data[i+1] = 0;
				data[i+2] = 0;

				byte[] new_bytes = new byte[4];
				
				// create binary strings based on bytes
				String b0 = String.format("%8s", Integer.toBinaryString(old_bytes[0] & 0xFF)).replace(' ', '0');
				String b1 = String.format("%8s", Integer.toBinaryString(old_bytes[1] & 0xFF)).replace(' ', '0');
				String b2 = String.format("%8s", Integer.toBinaryString(old_bytes[2] & 0xFF)).replace(' ', '0');
				String b3;
				
				// concatenate binary strings together into one long string
				String bin_string = b0 + b1 + b2;
				
				// separate our 3 bytes into 4 8-bit parts tacking on zeros to the left
				b0 = "00" + bin_string.substring(0, 6);
				b1 = "00" + bin_string.substring(6, 12);
				b2 = "00" + bin_string.substring(12, 18);
				b3 = "00" + bin_string.substring(18, 24);
				
				// parse binary strings to create bytes
				new_bytes[0] = (byte) Integer.parseInt(b0);
				new_bytes[1] = (byte) Integer.parseInt(b1);
				new_bytes[2] = (byte) Integer.parseInt(b2);
				new_bytes[3] = (byte) Integer.parseInt(b3);
								
				// create our encoded string from the new bytes
				encoded_string = new String(new_bytes);
			}// end if make sure we have enough bytes to split into 4 bytes
		}// end for loop over all our file bytes
		
		data = Arrays.copyOfRange(data, i-3, data.length); // add on additional 1 or 2 bytes 
		encoded_string += new String(data);
				
		return encoded_string;
	}
	
	public void handleMessage(String cmd, String s) {
		switch (cmd) {
		case "print":
			System.out.println(s);
			break;
		case "broadcast":
			if(!name.equals("admin"))
				System.out.println("Server Message: " + s.trim()); // dont print broadcast message on admins client
			break;
		case "exit":
			System.exit(-1);
			break;
			
		default:
			System.out.println("client side: unknown command received:" + cmd);
		}
	}
		
	public static void main(String[] args) {		
		new Thread(new Client()).start();
	} // end of main method
		
}// end of client class
