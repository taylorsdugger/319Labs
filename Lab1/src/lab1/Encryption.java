package lab1;

import java.io.IOException;

public class Encryption {

    public static String encode(String s, byte b) {
        return new String(xorWithKey(s.getBytes(), b));
    }

    public static String decode(String s, byte b) {
        return new String(xorWithKey(s.getBytes(), b));
    }

    public static String xorWithKey(byte[] a, byte key) {
        byte[] out = new byte[a.length];
        for (int i = 0; i < a.length; i++) {
            out[i] = (byte) (a[i] ^ key);
        }
        return new String(out);
    }

}