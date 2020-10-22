package cn.ai4wms.macdemo;

import android.util.Base64;
import android.util.Log;

import androidx.core.content.res.TypedArrayUtils;

import java.security.MessageDigest;
import java.security.NoSuchAlgorithmException;
import java.security.SecureRandom;
import java.util.Arrays;

import javax.crypto.Cipher;
import javax.crypto.spec.IvParameterSpec;
import javax.crypto.spec.SecretKeySpec;


public final class JwtParse {
    private static final String TAG = "JwtParse";
    private static final String M_CHARSET = "utf-8";
    public static final String ALG = "AES/CBC/PKCS5Padding";
    private static final String SECRET_KEY = "MGQ0YzU3YWQzYWVjMzliN2NhZmIyZjllNGYxZjYy";

    public static String parse(String JwtToken) {
//        String payload = getPayload(JwtToken);

//        byte[] byteDecode = Base64.decode(payload, Base64.URL_SAFE);
        byte[] byteDecode = Base64.decode(JwtToken, Base64.URL_SAFE);
        String b64decode = new String(byteDecode);

        decrypt(b64decode);

        return b64decode;
    }

    public static String encrypt(String encStr) {
        try {
            Cipher cipher = Cipher.getInstance(ALG);

            // iv
            SecureRandom randomSecureRandom = new SecureRandom();
            byte[] iv = new byte[cipher.getBlockSize()];
            randomSecureRandom.nextBytes(iv);
            IvParameterSpec ivSpec = new IvParameterSpec(iv);


            // sec
            SecretKeySpec skspec = getKeySpec();
            cipher.init(Cipher.ENCRYPT_MODE, skspec, ivSpec);
            byte[] finalByteArray = cipher.doFinal(encStr.getBytes());

            byte[] ivsk = byteMerge(iv,finalByteArray);

            String finalValue = Base64.encodeToString(ivsk, Base64.URL_SAFE|Base64.NO_WRAP);
            return finalValue;
        } catch (Exception e) {
            e.printStackTrace();
            Log.d(TAG, "generateChecksum: " + e.getMessage());
        }

        return "";
    }

    public static String decrypt(String encStr) {
        byte[] encBytes = Base64.decode(encStr, Base64.URL_SAFE);
        byte[] iv = Arrays.copyOfRange(encBytes, 0, 16);
        byte[] contentBytes = Arrays.copyOfRange(encBytes, 16, encBytes.length);

        IvParameterSpec ivSpec = new IvParameterSpec(iv);
        SecretKeySpec skspec = getKeySpec();

        try {
            Cipher cipher = Cipher.getInstance(ALG);
            cipher.init(Cipher.DECRYPT_MODE, skspec, ivSpec);
            byte[] finalByteArray = cipher.doFinal(contentBytes);
            String finalValue = new String(finalByteArray, M_CHARSET);

            return finalValue;
        } catch (Exception e) {
            e.printStackTrace();
            Log.d(TAG, "decrypt: " + e.getMessage());
        }

        return "";
    }

    private static byte[] byteMerge(byte[] first, byte[] second) {
        byte[] newbyte = new byte[first.length + second.length];
        System.arraycopy(first, 0, newbyte, 0, second.length);
        System.arraycopy(second, 0, newbyte, first.length, second.length);
        return newbyte;
    }

    private static SecretKeySpec getKeySpec() {
        try {
            // sec
            MessageDigest mDigest = MessageDigest.getInstance("SHA-512");
            byte[] digestSeed = mDigest.digest(SECRET_KEY.getBytes());
            byte[] seedEncArray = Arrays.copyOf(digestSeed, 32);
            SecretKeySpec skspec = new SecretKeySpec(seedEncArray, "AES");
            return skspec;
        } catch (NoSuchAlgorithmException e) {
            e.printStackTrace();
        }
        return null;
    }

    private static String getPayload(String token) {
        String[] info = token.split("\\.");
        if (info.length != 3) {
            return "";
        } else {
            return info[1];
        }
    }
}