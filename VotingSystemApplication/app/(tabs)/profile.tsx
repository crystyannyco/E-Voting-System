import React, { useEffect, useState, useCallback } from 'react';
import { View, Text, StyleSheet, Image, TouchableOpacity, ScrollView, ActivityIndicator, RefreshControl } from 'react-native';
import AsyncStorage from '@react-native-async-storage/async-storage';
import { useRouter } from 'expo-router';

const departmentMap: Record<string | number, string> = {
  1: 'College of Computer Studies',
  2: 'College of Engineering and Architecture',
  3: 'College of Health Sciences',
  4: 'College of Tourism, Hospitality and Business Management',
  5: 'College of Technological and Developmental Education',
  6: 'College of Arts and Sciences'
};

const programMap: Record<string | number, { name: string, acronym: string }> = {
  1: { name: 'Bachelor of Science in Information Technology', acronym: 'BSIT' },
  2: { name: 'Bachelor of Science in Computer Science', acronym: 'BSCS' },
  3: { name: 'Bachelor of Science in Information Systems', acronym: 'BSIS' },
  4: { name: 'Bachelor of Library Information Science', acronym: 'BLIS' },
  5: { name: 'Bachelor of Science in Electrical Engineering', acronym: 'BSEE' },
  6: { name: 'Bachelor of Science in Computer Engineering', acronym: 'BSCpE' },
  7: { name: 'Bachelor of Science in Civil Engineering', acronym: 'BSCE' },
  8: { name: 'Bachelor of Science in Electronics Engineering', acronym: 'BSECE' },
  9: { name: 'Bachelor of Science in Mechanical Engineering', acronym: 'BSME' },
  10: { name: 'Bachelor of Science in Architecture', acronym: 'BSA' },
  11: { name: 'Bachelor of Science in Nursing', acronym: 'BSN' },
  12: { name: 'Bachelor of Science in Midwifery', acronym: 'BSM' },
  13: { name: 'Bachelor of Science in Tourism Management', acronym: 'BSTM' },
  14: { name: 'Bachelor of Science in Hospitality Management', acronym: 'BSHM' },
  15: { name: 'Bachelor of Science in Office Administration', acronym: 'BSOA' },
  16: { name: 'Bachelor of Science in Entrepreneurship', acronym: 'BSE' },
  17: { name: 'Bachelor of Science in Business Administration major in Financial Management', acronym: 'BSBA-FM' },
  18: { name: 'Bachelor of Secondary Education', acronym: 'BSEd' },
  19: { name: 'Bachelor of Elementary Education', acronym: 'BEEd' },
  20: { name: 'Bachelor of Technical-Vocational Teacher Education', acronym: 'BTVTE' },
  21: { name: 'Bachelor of Special Needs Education', acronym: 'BSNE' },
  22: { name: 'Bachelor of Physical Education', acronym: 'BPE' },
  23: { name: 'Bachelor of Culture and Arts Education', acronym: 'BCAE' },
  24: { name: 'Bachelor of Arts in English Language Studies', acronym: 'BAELS' },
  25: { name: 'Bachelor in Human Services', acronym: 'BHS' },
  26: { name: 'Bachelor of Science in Development Communication', acronym: 'BSDC' },
  27: { name: 'Bachelor of Public Administration', acronym: 'BPA' },
  28: { name: 'Bachelor of Science in Mathematics', acronym: 'BSM' },
  29: { name: 'Bachelor of Science in Applied Mathematics', acronym: 'BSAM' }
};

const sectionMap: Record<string | number, string> = {
  1: 'A',
  2: 'B',
  3: 'C',
  4: 'D',
  5: 'E',
  6: 'F',
  7: 'G',
  8: 'H'
};

const genderMap: Record<string | number, string> = {
  1: 'Male',
  2: 'Female'
};

interface StudentProfile {
  FirstName?: string;
  MiddleName?: string;
  LastName?: string;
  Email?: string;
  PhoneNumber?: string;
  StudentID?: string;
  Gender?: string | number;
  genderText?: string;
  CivilStatus?: string;
  Birthdate?: string;
  Address?: string;
  Year?: string;
  Section?: string;
  sectionName?: string;
  Department?: string | number;
  Course?: string | number;
  departmentName?: string;
  programAcronym?: string;
  programName?: string;
  ProfileUrl?: string;
}

export default function ProfileScreen() {
  const router = useRouter();
  const [student, setStudent] = useState<StudentProfile | null>(null);
  const [loading, setLoading] = useState(true);
  const [refreshing, setRefreshing] = useState(false);

  const fetchStudent = async () => {
    try {
      const userData = await AsyncStorage.getItem('userData');
      if (userData) {
        const parsedData = JSON.parse(userData);
        // Format the data for display
        parsedData.genderText = genderMap[parsedData.Gender] || parsedData.genderText;
        parsedData.departmentName = departmentMap[parsedData.Department] || parsedData.departmentName;
        parsedData.sectionName = sectionMap[parsedData.Section] || parsedData.sectionName;
        if (parsedData.Course && programMap[parsedData.Course]) {
          parsedData.programName = programMap[parsedData.Course].name;
          parsedData.programAcronym = programMap[parsedData.Course].acronym;
        }
        setStudent(parsedData);
      }
    } catch (error) {
      console.error('Error fetching user data:', error);
    } finally {
      setLoading(false);
      setRefreshing(false);
    }
  };

  useEffect(() => {
    fetchStudent();
  }, []);

  const onRefresh = useCallback(() => {
    setRefreshing(true);
    fetchStudent();
  }, []);

  const handleLogout = async () => {
    try {
      await AsyncStorage.removeItem('userData');
      router.replace('/login');
    } catch (error) {
      console.error('Logout error:', error);
    }
  };

  const formatDate = (dateString: string) => {
    if (!dateString) return '-';
    const date = new Date(dateString);
    return date.toLocaleDateString('en-US', {
      year: 'numeric',
      month: 'long',
      day: 'numeric'
    });
  };

  if (loading) {
    return (
      <View style={styles.loadingContainer}>
        <ActivityIndicator size="large" color="#2563eb" />
      </View>
    );
  }

  return (
    <ScrollView
      contentContainerStyle={styles.scrollContainer}
      refreshControl={
        <RefreshControl refreshing={refreshing} onRefresh={onRefresh} />
      }
    >
      {/* <Text style={styles.settingsTitle}>Profile Settings</Text> */}
      <View style={styles.avatarContainer}>
        <View style={styles.avatarWrapper}>
          <Image
            source={
              student?.ProfileUrl
                ? { uri: student.ProfileUrl }
                : require('../../assets/images/default-profile.png')
            }
            style={styles.avatar}
          />
        </View>
        <Text style={styles.studentName} numberOfLines={1} ellipsizeMode="tail">
          {student ? `${student.FirstName || ''} ${student.MiddleName || ''} ${student.LastName || ''}`.replace(/\s+/g, ' ').trim() : 'Student Name'}
        </Text>
        <Text style={styles.studentEmail} numberOfLines={1} ellipsizeMode="tail">
          {student?.Email || 'cspc@my.cspc.edu.ph'}
        </Text>
        <Text style={styles.infoText}>{student?.PhoneNumber || '-'}</Text>
      </View>
      <View style={styles.infoSection}>
        <Text style={styles.infoLabel}>Full Name</Text>
        <Text style={styles.infoText}>{student ? `${student.FirstName || ''} ${student.MiddleName || ''} ${student.LastName || ''}`.replace(/\s+/g, ' ').trim() : '-'}</Text>
        <View style={styles.divider} />
        <Text style={styles.infoLabel}>Student ID</Text>
        <Text style={styles.infoText}>{student?.StudentID || '-'}</Text>
        <View style={styles.divider} />
        <View style={styles.rowBetween}>
          <View style={{flex:1}}>
            <Text style={styles.infoLabel}>Gender</Text>
            <Text style={styles.infoText}>{student?.genderText || '-'}</Text>
          </View>
          <View style={{flex:1}}>
            <Text style={styles.infoLabel}>Birthdate</Text>
            <Text style={styles.infoText}>{formatDate(student?.Birthdate || '')}</Text>
          </View>
        </View>
        <View style={styles.divider} />
        <View style={styles.rowBetween}>
          <View style={{flex:1}}>
            <Text style={styles.infoLabel}>Year Level</Text>
            <Text style={styles.infoText}>{student?.Year ? `${student.Year}` : '-'}</Text>
          </View>
          <View style={{flex:1}}>
            <Text style={styles.infoLabel}>Section</Text>
            <Text style={styles.infoText}>{student?.sectionName || '-'}</Text>
          </View>
        </View>
        <View style={styles.divider} />
        <Text style={styles.infoLabel}>Department</Text>
        <Text style={styles.infoText}>{student?.departmentName || '-'}</Text>
        <View style={styles.divider} />
        <Text style={styles.infoLabel}>Program</Text>
        <Text style={styles.infoText}>{student?.programName || '-'}</Text>
        <Text style={[styles.infoText, { color: '#2563EB', fontSize: 14 }]}>{student?.programAcronym || '-'}</Text>
      </View>

      {/* <TouchableOpacity 
        style={styles.editButton} 
        onPress={() => router.push('/edit-profile')}
      >
        <Text style={styles.editButtonText}>Edit Profile</Text>
      </TouchableOpacity> */}

      <TouchableOpacity style={styles.logoutButton} onPress={handleLogout} accessibilityLabel="Logout">
        <Text style={styles.logoutText}>Logout</Text>
      </TouchableOpacity>
    </ScrollView>
  );
}

const styles = StyleSheet.create({
  scrollContainer: {
    flexGrow: 1,
    backgroundColor: '#fff',
    alignItems: 'center',
    paddingTop: 40,
    paddingBottom: 30,
    minHeight: '100%',
  },
  loadingContainer: {
    flex: 1,
    justifyContent: 'center',
    alignItems: 'center',
    backgroundColor: '#fff',
  },
  settingsTitle: {
    fontSize: 22,
    fontWeight: 'bold',
    marginBottom: 18,
    alignSelf: 'center',
    color: '#1E293B',
    letterSpacing: 0.5,
  },
  avatarContainer: {
    alignItems: 'center',
    marginBottom: 20,
    width: '100%',
  },
  avatarWrapper: {
    position: 'relative',
    marginBottom: 8,
  },
  avatar: {
    width: 110,
    height: 110,
    borderRadius: 55,
    backgroundColor: '#e5e7eb',
  },
  studentName: {
    fontSize: 21,
    fontWeight: 'bold',
    marginTop: 4,
    marginBottom: 2,
    textAlign: 'center',
    color: '#1e293b',
    maxWidth: 260,
  },
  studentEmail: {
    fontSize: 15,
    color: '#2563eb',
    marginBottom: 8,
    textAlign: 'center',
    maxWidth: 260,
  },
  infoSection: {
    width: '92%',
    backgroundColor: '#f9fafb',
    borderRadius: 14,
    padding: 20,
    marginBottom: 30,
    elevation: 2,
    shadowColor: '#000',
    shadowOpacity: 0.06,
    shadowRadius: 4,
    shadowOffset: { width: 0, height: 2 },
  },
  infoLabel: {
    fontSize: 13,
    color: '#64748b',
    marginTop: 8,
    marginBottom: 2,
    fontWeight: '600',
  },
  infoText: {
    fontSize: 16,
    color: '#222',
    marginBottom: 2,
    fontWeight: '500',
  },
  divider: {
    height: 1,
    backgroundColor: '#e5e7eb',
    marginVertical: 8,
  },
  rowBetween: {
    flexDirection: 'row',
    justifyContent: 'space-between',
    alignItems: 'flex-start',
    marginVertical: 4,
    gap: 8,
  },
  editButton: {
    backgroundColor: '#2563eb',
    borderRadius: 12,
    paddingVertical: 16,
    paddingHorizontal: 40,
    alignSelf: 'stretch',
    marginHorizontal: 24,
    marginBottom: 12,
    shadowColor: '#2563eb',
    shadowOpacity: 0.15,
    shadowRadius: 6,
    shadowOffset: { width: 0, height: 2 },
  },
  editButtonText: {
    color: '#fff',
    fontSize: 18,
    fontWeight: 'bold',
    textAlign: 'center',
    letterSpacing: 0.5,
  },
  logoutButton: {
    backgroundColor: '#b91c1c',
    borderRadius: 12,
    paddingVertical: 16,
    paddingHorizontal: 40,
    alignSelf: 'stretch',
    marginHorizontal: 24,
    marginTop: 'auto',
    shadowColor: '#b91c1c',
    shadowOpacity: 0.15,
    shadowRadius: 6,
    shadowOffset: { width: 0, height: 2 },
  },
  logoutText: {
    color: '#fff',
    fontSize: 18,
    fontWeight: 'bold',
    textAlign: 'center',
    letterSpacing: 0.5,
  },
});
