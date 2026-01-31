import { useState, useEffect } from 'react';
import {
  TextInput,
  View,
  Text,
  TouchableOpacity,
  KeyboardAvoidingView,
  Platform,
  Image,
  SafeAreaView,
  ActivityIndicator,
  Alert,
  StyleSheet
} from 'react-native';
import { router } from 'expo-router';
import AsyncStorage from '@react-native-async-storage/async-storage';

// Base URL for your API
const API_URL = "http://172.16.116.113:8080/api";

export default function LoginScreen() {
  const [email, setEmail] = useState("");
  const [password, setPassword] = useState("");
  const [loading, setLoading] = useState(false);
  const [error, setError] = useState("");
  const [showPassword, setShowPassword] = useState(false);
  const [checkingLogin, setCheckingLogin] = useState(true); // NEW: for initial login check

  // Check if user is already logged in on component mount
  useEffect(() => {
    const checkLoginStatus = async () => {
      try {
        const userData = await AsyncStorage.getItem("userData");
        if (userData) {
          // User is already logged in, redirect to dashboard
          router.replace("/home");
        }
      } catch (error) {
        console.error("Error checking login status:", error);
      } finally {
        setCheckingLogin(false); // Always stop checking
      }
    };
    checkLoginStatus();
  }, []);

  const handleLogin = async () => {
    // Reset error state
    setError("");
  
    // Validate inputs
    if (!email || !password) {
      setError("Please enter both email and password");
      return;
    }
  
    try {
      setLoading(true); // Make sure this is uncommented
      
      console.log(`Attempting to connect to: ${API_URL}/student/login`);
      console.log("Login credentials:", { email });
      console.log("Login credentials:", { password }); // For debugging, don't log passwords
      
      const response = await fetch(`${API_URL}/student/login`, {
        method: "POST",
        headers: {
          "Content-Type": "application/json",
        },
        body: JSON.stringify({
          email: email,
          password: password,
        }),
      });
      
      console.log("Response status:", response.status);
      
      // Check if the request was actually successful
      if (!response.ok) {
        throw new Error(`HTTP error! Status: ${response.status}`);
      }
  
      const data = await response.json();
      console.log("Response data:", data);
  
      if (data.meta.code !== 200) {
        setError(data.meta.message || "Login failed");
        setLoading(false);
        return;
      }
  
      // Save user data in AsyncStorage
      await AsyncStorage.setItem("userData", JSON.stringify(data.data));
      console.log("User data saved successfully");
  
      // Navigate to dashboard
      router.replace("/home");
    } catch (error) {
      // Provide more specific error messages based on the type of error
      if (error instanceof Error && error.message.includes("Network request failed")) {
        setError("Network error. Please check your internet connection or API server.");
      } else if (error instanceof Error) {
        setError(error.message || "Failed to login. Please try again.");
      } else {
        setError("Failed to login. Please try again.");
      }
    } finally {
      setLoading(false);
    }
  };

  if (checkingLogin) {
    // Show a full-screen loading indicator while checking login
    return (
      <SafeAreaView style={{ flex: 1, justifyContent: 'center', alignItems: 'center', backgroundColor: '#fff' }}>
        <ActivityIndicator size="large" color="#1E40AF" />
        <Text style={{ marginTop: 16, color: '#1E40AF', fontSize: 16 }}>Checking login status...</Text>
      </SafeAreaView>
    );
  }

  return (
    <SafeAreaView style={styles.safeArea}>
      <KeyboardAvoidingView
        behavior={Platform.OS === 'ios' ? 'padding' : 'height'}
        style={styles.container}
      >
        {loading && (
          <View style={{
            ...StyleSheet.absoluteFillObject,
            backgroundColor: 'rgba(255,255,255,0.7)',
            justifyContent: 'center',
            alignItems: 'center',
            zIndex: 10,
          }}>
            <ActivityIndicator size="large" color="#1E40AF" />
            <Text style={{ marginTop: 16, color: '#1E40AF', fontSize: 16 }}>Loading...</Text>
          </View>
        )}
        <View style={styles.formContainer} pointerEvents={loading ? 'none' : 'auto'}>
          <View style={styles.logoContainer}>
            <Image
              source={require('../assets/images/logos/cspc-logo.png')}
              style={styles.logo}
              resizeMode="contain"
            />
            <Image
              source={require('../assets/images/logos/scc-logo.png')}
              style={styles.logo}
              resizeMode="contain"
            />
          </View>
          <Text style={styles.title}>CSPC E-Vote</Text>

          <TextInput
            style={[styles.input, error && email === "" && styles.inputError]}
            placeholder="Email Address"
            value={email}
            onChangeText={setEmail}
            autoCapitalize="none"
            keyboardType="email-address"
          />

          <View style={{ width: '100%', position: 'relative' }}>
            <TextInput
              style={[styles.input, error && password === "" && styles.inputError]}
              placeholder="Password"
              value={password}
              onChangeText={setPassword}
              secureTextEntry={!showPassword}
            />
            <TouchableOpacity
              style={{ position: 'absolute', right: 15, top: 12, zIndex: 2 }}
              onPress={() => setShowPassword((prev) => !prev)}
            >
              <Text style={{ color: '#1E40AF', fontWeight: 'bold', fontSize: 14 }}>
                {showPassword ? 'Hide' : 'Show'}
              </Text>
            </TouchableOpacity>
          </View>

          {error ? <Text style={styles.errorText}>{error}</Text> : null}

          {/* <TouchableOpacity onPress={() => router.push('forgot-password')} style={styles.forgotPassword}>
            <Text style={styles.forgotPasswordText}>Forgot password?</Text>
          </TouchableOpacity> */}

          <TouchableOpacity
            style={[styles.button, loading && styles.buttonDisabled]}
            onPress={handleLogin}
            disabled={loading}
          >
            {loading ? (
              <ActivityIndicator color="#FFFFFF" />
            ) : (
              <Text style={styles.buttonText}>Login</Text>
            )}
          </TouchableOpacity>

          <View style={styles.dividerContainer}>
            <View style={styles.divider} />
            <Text style={styles.dividerText}>Or continue with</Text>
            <View style={styles.divider} />
          </View>

          <TouchableOpacity style={styles.cspcEmailButton}>
            <Image
              source={require('../assets/images/logos/cspc-logo.png')}
              style={styles.smallLogo}
              resizeMode="contain"
            />
            <Text style={styles.cspcEmailButtonText}>Login with CSPC Email</Text>
          </TouchableOpacity>
        </View>
      </KeyboardAvoidingView>
    </SafeAreaView>
  );
}

const styles = StyleSheet.create({
  safeArea: {
    flex: 1,
    backgroundColor: '#fff',
  },
  container: {
    flex: 1,
    backgroundColor: '#fff',
  },
  formContainer: {
    flex: 1,
    padding: 20,
    justifyContent: 'center',
    alignItems: 'center',
    width: '100%',
  },
  logoContainer: {
    flexDirection: 'row',
    justifyContent: 'center',
    alignItems: 'center',
    marginBottom: 20,
    gap: 20,
  },
  logo: {
    width: 40,
    height: 40,
    resizeMode: 'contain',
  },
  title: {
    fontSize: 28,
    fontFamily: 'Inter-Bold',
    marginBottom: 30,
    color: '#000',
  },
  input: {
    width: '100%',
    height: 50,
    borderWidth: 1,
    borderColor: '#E5E7EB',
    borderRadius: 8,
    paddingHorizontal: 15,
    marginBottom: 15,
    fontSize: 16,
    backgroundColor: '#F9FAFB',
    fontFamily: 'Inter-Regular',
  },
  inputError: {
    borderColor: '#DC2626',
    backgroundColor: '#FEE2E2',
  },
  button: {
    width: '100%',
    height: 50,
    backgroundColor: '#1E40AF',
    borderRadius: 8,
    justifyContent: 'center',
    alignItems: 'center',
    marginTop: 10,
  },
  buttonDisabled: {
    backgroundColor: '#93C5FD',
  },
  buttonText: {
    color: '#fff',
    fontSize: 16,
    fontWeight: '600',
    fontFamily: 'Inter-SemiBold',
  },
  forgotPassword: {
    alignSelf: 'flex-start',
    marginBottom: 20,
  },
  forgotPasswordText: {
    color: '#1E40AF',
    fontSize: 14,
    fontFamily: 'Inter-Regular',
  },
  dividerContainer: {
    flexDirection: 'row',
    alignItems: 'center',
    width: '100%',
    marginVertical: 30,
  },
  divider: {
    flex: 1,
    height: 1,
    backgroundColor: '#E5E7EB',
  },
  dividerText: {
    color: '#6B7280',
    paddingHorizontal: 10,
    fontSize: 14,
  },
  cspcEmailButton: {
    width: '100%',
    height: 50,
    flexDirection: 'row',
    alignItems: 'center',
    justifyContent: 'center',
    borderWidth: 1,
    borderColor: '#E5E7EB',
    borderRadius: 8,
    backgroundColor: '#fff',
  },
  smallLogo: {
    width: 20,
    height: 20,
    marginRight: 10,
  },
  cspcEmailButtonText: {
    color: '#000',
    fontSize: 16,
    fontFamily: 'Inter-Medium',
    marginLeft: 10,
  },
  errorText: {
    color: '#DC2626',
    fontSize: 14,
    marginBottom: 10,
    fontFamily: 'Inter-Medium',
    alignSelf: 'flex-start',
    width: '100%',
    paddingLeft: 5,
  },
});