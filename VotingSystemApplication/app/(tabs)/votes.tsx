import React, { useEffect, useState } from 'react';
import { View, Text, StyleSheet, ActivityIndicator, FlatList, TouchableOpacity, ScrollView, Modal, RefreshControl } from 'react-native';
import AsyncStorage from '@react-native-async-storage/async-storage';
import { router, useFocusEffect } from 'expo-router';

export default function Votes() {
  const [elections, setElections] = useState<any[]>([]);
  const [universityElections, setUniversityElections] = useState<any[]>([]);
  const [departmentElections, setDepartmentElections] = useState<any[]>([]);
  const [selectedElection, setSelectedElection] = useState<any>(null);
  const [electionInfo, setElectionInfo] = useState<any>(null);
  const [loading, setLoading] = useState(true);
  const [refreshing, setRefreshing] = useState(false);
  const [loadingElectionInfo, setLoadingElectionInfo] = useState(false);
  const [student, setStudent] = useState<any>(null);
  const [showAlreadyVotedModal, setShowAlreadyVotedModal] = useState(false);
  const [alreadyVotedElection, setAlreadyVotedElection] = useState<string | null>(null);
  const [showClosedElectionModal, setShowClosedElectionModal] = useState(false);
  const [showComingSoonModal, setShowComingSoonModal] = useState(false);
  const [votingStatus, setVotingStatus] = useState<{[key: string]: boolean}>({});

  const formatDate = (dateString: string) => {
    if (!dateString) return '-';
    const date = new Date(dateString);
    return date.toLocaleDateString('en-US', {
      year: 'numeric',
      month: 'long',
      day: 'numeric'
    });
  };

  // Function to check voting status for all elections
  const checkVotingStatus = async (elections: any[], studentId: string) => {
    const status: {[key: string]: boolean} = {};
    for (const election of elections) {
      try {
        const res = await fetch(`http://172.16.116.113:8080/api/has-voted?student_id=${studentId}&election_id=${election.ElectionID}`);
        const data = await res.json();
        status[election.ElectionID] = data?.data?.hasVoted === true;
      } catch (error) {
        console.error(`Error checking vote status for election ${election.ElectionID}:`, error);
        status[election.ElectionID] = false;
      }
    }
    setVotingStatus(status);
  };

  const fetchStudentAndElections = async () => {
    setLoading(true);
    try {
      const studentData = await AsyncStorage.getItem('userData');
      let studentObj = null;
      if (studentData) {
        studentObj = JSON.parse(studentData);
        setStudent(studentObj);
      }
      const res = await fetch('http://172.16.116.113:8080/api/elections');
      const data = await res.json();
      if (data && data.data && studentObj) {
        const uniElections = data.data.filter((e: any) => e.election.Department == 0).map((uniElection: any) => ({
          id: uniElection.election.ElectionID,
          title: uniElection.election.ElectionName,
          rawStart: uniElection.election.Start,
          rawEnd: uniElection.election.End,
          date: `${formatDate(uniElection.election.Start)} - ${formatDate(uniElection.election.End)}`,
          status: 'Active',
          department: uniElection.departmentName,
          turnout: uniElection.turnout,
          ElectionID: uniElection.election.ElectionID,
        }));
        setUniversityElections(uniElections);

        const deptElections = data.data.filter((e: any) => e.election.Department == studentObj.Department).map((deptElection: any) => ({
          id: deptElection.election.ElectionID,
          title: deptElection.election.ElectionName,
          rawStart: deptElection.election.Start,
          rawEnd: deptElection.election.End,
          date: `${formatDate(deptElection.election.Start)} - ${formatDate(deptElection.election.End)}`,
          status: 'Active',
          department: deptElection.departmentName,
          turnout: deptElection.turnout,
          ElectionID: deptElection.election.ElectionID,
        }));
        setDepartmentElections(deptElections);

        const allElections = [...uniElections, ...deptElections];
        setElections(allElections);
        await checkVotingStatus(allElections, studentObj.StudentID);
        
        if (allElections.length === 1) {
          setSelectedElection(allElections[0]);
          const election = allElections[0];
          const now = new Date();
          const startDate = new Date(election.rawStart);
          const endDate = new Date(election.rawEnd);
          if (now < startDate) {
            setShowComingSoonModal(true);
          } else if (now > endDate) {
            setShowClosedElectionModal(true);
          } else if (votingStatus[election.ElectionID]) {
            setAlreadyVotedElection(election.title || election.ElectionName);
            setShowAlreadyVotedModal(true);
          } else {
            router.push({
              pathname: '/cast-vote',
              params: {
                electionId: election.ElectionID,
                electionTitle: election.title,
                electionStart: election.rawStart,
                electionEnd: election.rawEnd,
                electionStatus: (() => {
                  const now = new Date();
                  const startDate = new Date(election.rawStart);
                  const endDate = new Date(election.rawEnd);
                  if (now < startDate) return 'Coming Soon';
                  if (now > endDate) return 'Closed';
                  return 'Ongoing';
                })(),
              },
            });
          }
        } else {
          setSelectedElection(null);
        }
      } else {
        setElections([]);
        setUniversityElections([]);
        setDepartmentElections([]);
      }
    } catch (err) {
      setElections([]);
      setUniversityElections([]);
      setDepartmentElections([]);
    } finally {
      setLoading(false);
      setRefreshing(false);
    }
  };

  useEffect(() => {
    fetchStudentAndElections();
  }, []);

  const onRefresh = React.useCallback(() => {
    setRefreshing(true);
    fetchStudentAndElections();
  }, []);

  useFocusEffect(
    React.useCallback(() => {
      if (elections.length === 1) {
        const election = elections[0];
        const now = new Date();
        const startDate = new Date(election.rawStart);
        const endDate = new Date(election.rawEnd);
        if (now < startDate) {
          setShowComingSoonModal(true);
        } else if (now > endDate) {
          setShowClosedElectionModal(true);
        } else {
          router.replace({
            pathname: '/cast-vote',
            params: {
              electionId: election.ElectionID,
              electionTitle: election.title,
              electionStart: election.rawStart,
              electionEnd: election.rawEnd,
              electionStatus: (() => {
                const startDate = new Date(election.rawStart);
                const endDate = new Date(election.rawEnd);
                const now = new Date();
                if (now < startDate) return 'Upcoming';
                if (now > endDate) return 'Ended';
                return 'Ongoing';
              })(),
            },
          });
        }
      }
    }, [elections, student])
  );

  useEffect(() => {
    if (!selectedElection) return;
    const fetchElectionInfo = async () => {
      setLoadingElectionInfo(true);
      try {
        const res = await fetch(`http://172.16.116.113:8080/api/election-info/${selectedElection.ElectionID}`);
        const data = await res.json();
        setElectionInfo(data.data || null);
      } catch (err) {
        setElectionInfo(null);
      } finally {
        setLoadingElectionInfo(false);
      }
    };
    fetchElectionInfo();
  }, [selectedElection]);

  const handleVoteClick = async (election: any) => {
    if (!student) return;
    
    const now = new Date();
    const startDate = new Date(election.rawStart);
    const endDate = new Date(election.rawEnd);
    
    if (now < startDate) {
      setShowComingSoonModal(true);
      return;
    }
    
    if (now > endDate) {
      setShowClosedElectionModal(true);
      return;
    }

    try {
      const res = await fetch(`http://172.16.116.113:8080/api/student/check-voting-status/${student.StudentID}/${election.ElectionID}`);
      const data = await res.json();
      
      if (data?.data?.hasVoted) {
        setAlreadyVotedElection(election.title);
        setShowAlreadyVotedModal(true);
        return;
      }
    } catch (error) {
      console.error('Error checking voting status:', error);
    }

    router.push({
      pathname: '/cast-vote',
      params: {
        electionId: election.ElectionID,
        electionTitle: election.title,
        electionStart: election.rawStart,
        electionEnd: election.rawEnd,
        electionStatus: (() => {
          const now = new Date();
          const startDate = new Date(election.rawStart);
          const endDate = new Date(election.rawEnd);
          if (now < startDate) return 'Coming Soon';
          if (now > endDate) return 'Closed';
          return 'Ongoing';
        })(),
      },
    });
  };

  if (loading) {
    return <ActivityIndicator size="large" color="#3B82F6" style={{ flex: 1, justifyContent: 'center', alignItems: 'center' }} />;
  }

  if (elections.length === 1) {
    return null;
  }

  if (!elections.length) {
    return (
      <View style={[styles.container, { justifyContent: 'center', alignItems: 'center' }]}> 
        <View style={{ alignItems: 'center' }}>
          <Text style={{ fontSize: 48, color: '#CBD5E1', marginBottom: 8 }}>🗳️</Text>
          <Text style={{ fontSize: 18, color: '#64748B', fontWeight: 'bold', marginBottom: 4 }}>No elections available</Text>
          <Text style={{ color: '#94A3B8', fontSize: 14, textAlign: 'center', maxWidth: 260 }}>
            There are currently no elections open for your department or university. Please check back later!
          </Text>
        </View>
      </View>
    );
  }

  return (
    <View style={styles.container}>
      <ScrollView
        contentContainerStyle={{ flexGrow: 1, alignItems: 'center', justifyContent: 'flex-start', padding: 24, backgroundColor: '#f3f4f6' }}
        showsVerticalScrollIndicator={false}
        refreshControl={
          <RefreshControl
            refreshing={refreshing}
            onRefresh={onRefresh}
            colors={['#2563EB']}
            tintColor="#2563EB"
            title="Refreshing..."
            titleColor="#64748B"
          />
        }
      >
        <Text style={{ fontSize: 28, fontWeight: 'bold', color: '#1E293B', marginBottom: 18, alignSelf: 'flex-start', letterSpacing: -1 }}>Elections</Text>
        {elections.length > 1 && (
          <View style={{ marginBottom: 24, width: '100%' }}>
            <Text style={{ fontSize: 18, fontWeight: 'bold', marginBottom: 8, color: '#2563EB' }}>Select Election</Text>
            {elections.map((election) => (
              <View key={election.ElectionID} style={{ marginBottom: 16, backgroundColor: '#fff', borderRadius: 16, padding: 18, borderWidth: 1, borderColor: '#E5E7EB', elevation: 2, shadowColor: '#000', shadowOpacity: 0.07, shadowRadius: 8, shadowOffset: { width: 0, height: 2 } }}>
                <Text style={{ fontWeight: 'bold', fontSize: 18, color: '#1E293B', marginBottom: 2 }}>{election.title}</Text>
                <Text style={{ fontSize: 13, color: '#64748B', marginBottom: 2 }}>{election.date}</Text>
                <Text style={{ color: '#64748B', fontSize: 13, marginBottom: 2 }}>{election.department}</Text>
                <View style={{ alignItems: 'flex-start', marginTop: 4, marginBottom: 10 }}>
                  {(() => {
                    const now = new Date();
                    const startDate = new Date(election.rawStart);
                    const endDate = new Date(election.rawEnd);
                    let statusLabel = 'Ongoing';
                    let statusColor = '#16A34A';
                    if (now < startDate) { statusLabel = 'Coming Soon'; statusColor = '#F59E42'; }
                    else if (now > endDate) { statusLabel = 'Closed'; statusColor = '#DC2626'; }
                    return (
                      <View style={{ backgroundColor: statusColor + '22', paddingHorizontal: 12, paddingVertical: 4, borderRadius: 8 }}>
                        <Text style={{ color: statusColor, fontWeight: 'bold', fontSize: 14 }}>{statusLabel}</Text>
                      </View>
                    );
                  })()}
                </View>
                <TouchableOpacity
                  style={{
                    backgroundColor: '#2563EB',
                    borderRadius: 8,
                    paddingVertical: 12,
                    alignItems: 'center',
                    marginTop: 4,
                    shadowColor: '#2563EB',
                    shadowOpacity: 0.15,
                    shadowRadius: 6,
                    shadowOffset: { width: 0, height: 2 },
                  }}
                  activeOpacity={0.85}
                  onPress={() => handleVoteClick(election)}
                >
                  <Text style={{ color: '#fff', fontWeight: 'bold', fontSize: 16, letterSpacing: 0.5 }}>Vote</Text>
                </TouchableOpacity>
              </View>
            ))}
          </View>
        )}
        {selectedElection && (
          <View style={{ width: '100%' }}>
            <View style={{ backgroundColor: '#fff', borderRadius: 16, padding: 20, marginBottom: 20, borderWidth: 1, borderColor: '#E5E7EB', elevation: 2, shadowColor: '#000', shadowOpacity: 0.07, shadowRadius: 8, shadowOffset: { width: 0, height: 2 } }}>
              <Text style={{ fontWeight: 'bold', fontSize: 20, color: '#1E293B', marginBottom: 4 }}>{selectedElection.title}</Text>
              <Text style={{ fontSize: 13, color: '#64748B', marginBottom: 2 }}>{selectedElection.date}</Text>
              <Text style={{ color: '#64748B', fontSize: 13, marginBottom: 2 }}>{selectedElection.department}</Text>
              <View style={{ alignItems: 'flex-start', marginTop: 8, marginBottom: 8 }}>
                {(() => {
                  const now = new Date();
                  const startDate = new Date(selectedElection.rawStart);
                  const endDate = new Date(selectedElection.rawEnd);
                  let statusLabel = 'Ongoing';
                  let statusColor = '#16A34A';
                  if (now < startDate) { statusLabel = 'Coming Soon'; statusColor = '#F59E42'; }
                  else if (now > endDate) { statusLabel = 'Closed'; statusColor = '#DC2626'; }
                  return (
                    <View style={{ backgroundColor: statusColor + '22', paddingHorizontal: 12, paddingVertical: 4, borderRadius: 8 }}>
                      <Text style={{ color: statusColor, fontWeight: 'bold', fontSize: 14 }}>{statusLabel}</Text>
                    </View>
                  );
                })()}
              </View>
              {selectedElection.turnout && (
                <View style={{ backgroundColor: '#F1F5F9', padding: 12, borderRadius: 12, marginTop: 10 }}>
                  <Text style={{ fontWeight: 'bold', fontSize: 14, color: '#1E293B', marginBottom: 3 }}>VOTES</Text>
                  <View style={{ flexDirection: 'row', height: 28, borderRadius: 8, overflow: 'hidden', backgroundColor: '#E5E7EB' }}>
                    <View style={{ flex: selectedElection.turnout.votedPercentage, backgroundColor: '#2563EB', justifyContent: 'center', alignItems: 'center' }}>
                      {selectedElection.turnout.votedPercentage > 0 ? (
                        <Text style={{ color: 'white', fontWeight: 'bold', fontSize: 13 }}>{selectedElection.turnout.votedPercentage}%</Text>
                      ) : null}
                    </View>
                    <View style={{ flex: selectedElection.turnout.notVotedPercentage, backgroundColor: '#DC2626', justifyContent: 'center', alignItems: 'center' }}>
                      {selectedElection.turnout.notVotedPercentage > 0 ? (
                        <Text style={{ color: 'white', fontWeight: 'bold', fontSize: 13 }}>{selectedElection.turnout.notVotedPercentage}%</Text>
                      ) : null}
                    </View>
                  </View>
                  <View style={{ flexDirection: 'row', marginTop: 8, justifyContent: 'center', alignItems: 'center' }}>
                    <View style={{ flexDirection: 'row', alignItems: 'center', marginRight: 12 }}>
                      <View style={{ width: 10, height: 10, borderRadius: 5, backgroundColor: '#2563EB', marginRight: 6 }} />
                      <Text style={{ fontSize: 12, color: '#1E293B' }}>ALREADY VOTED</Text>
                    </View>
                    <View style={{ flexDirection: 'row', alignItems: 'center' }}>
                      <View style={{ width: 10, height: 10, borderRadius: 5, backgroundColor: '#DC2626', marginRight: 6 }} />
                      <Text style={{ fontSize: 12, color: '#1E293B' }}>NOT YET VOTED</Text>
                    </View>
                  </View>
                </View>
              )}
            </View>
            <Text style={{ fontSize: 20, fontWeight: 'bold', marginBottom: 12, color: '#2563EB' }}>{selectedElection.ElectionName || selectedElection.title} Candidates</Text>
            {loadingElectionInfo ? (
              <ActivityIndicator size="small" color="#3B82F6" />
            ) : electionInfo && electionInfo.candidates && electionInfo.candidates.length ? (
              <FlatList
                data={electionInfo.candidates}
                keyExtractor={(item) => item.CandidateID.toString()}
                renderItem={({ item }) => (
                  <View key={item.CandidateID} style={{ flexDirection: 'row', alignItems: 'center', padding: 16, backgroundColor: '#fff', borderRadius: 12, marginBottom: 10, elevation: 2, shadowColor: '#000', shadowOpacity: 0.07, shadowRadius: 8, shadowOffset: { width: 0, height: 2 } }}>
                    <View style={{ width: 48, height: 48, borderRadius: 24, backgroundColor: '#E0E7FF', justifyContent: 'center', alignItems: 'center', marginRight: 16 }}>
                      <Text style={{ fontSize: 22, fontWeight: 'bold', color: '#6366F1' }}>{item.FirstName[0]}</Text>
                    </View>
                    <View style={{ flex: 1 }}>
                      <Text style={{ fontWeight: 'bold', fontSize: 16, color: '#1E293B' }}>{item.FirstName} {item.MiddleName} {item.LastName}</Text>
                      <Text style={{ color: '#3B82F6', fontSize: 14, marginTop: 2 }}>{item.PositionName}</Text>
                      <Text style={{ color: '#64748b', fontSize: 13, marginTop: 1 }}>{item.PartylistName || 'Independent'}</Text>
                    </View>
                  </View>
                )}
                style={{ marginBottom: 10 }}
                scrollEnabled={false}
              />
            ) : (
              <Text style={{ color: '#64748B', fontSize: 15, textAlign: 'center', marginTop: 12 }}>No candidates for this election.</Text>
            )}
          </View>
        )}
        {/* Closed Election Modal */}
        <Modal
          visible={showClosedElectionModal}
          transparent
          animationType="fade"
          onRequestClose={() => setShowClosedElectionModal(false)}
        >
          <View style={{ flex: 1, backgroundColor: 'rgba(0,0,0,0.4)', justifyContent: 'center', alignItems: 'center' }}>
            <View style={{ backgroundColor: '#fff', borderRadius: 16, padding: 24, width: '85%', maxWidth: 400, alignItems: 'center' }}>
              <Text style={{ fontWeight: 'bold', fontSize: 20, color: '#DC2626', marginBottom: 10 }}>Election Closed</Text>
              <Text style={{ color: '#1E293B', fontSize: 15, marginBottom: 18, textAlign: 'center' }}>This election is already closed. You cannot vote.</Text>
              <TouchableOpacity 
                onPress={() => setShowClosedElectionModal(false)} 
                style={{ 
                  backgroundColor: '#2563EB', 
                  borderRadius: 8, 
                  paddingVertical: 10, 
                  paddingHorizontal: 32,
                  shadowColor: '#2563EB',
                  shadowOffset: { width: 0, height: 2 },
                  shadowOpacity: 0.15,
                  shadowRadius: 4,
                  elevation: 2
                }}
              >
                <Text style={{ color: '#fff', fontWeight: 'bold', fontSize: 16 }}>Okay</Text>
              </TouchableOpacity>
            </View>
          </View>
        </Modal>

        {/* Already Voted Modal */}
        <Modal
          visible={showAlreadyVotedModal}
          transparent
          animationType="fade"
          onRequestClose={() => setShowAlreadyVotedModal(false)}
        >
          <View style={{ flex: 1, backgroundColor: 'rgba(0,0,0,0.4)', justifyContent: 'center', alignItems: 'center' }}>
            <View style={{ backgroundColor: '#fff', borderRadius: 16, padding: 24, width: '85%', maxWidth: 400, alignItems: 'center' }}>
              <Text style={{ fontWeight: 'bold', fontSize: 20, color: '#DC2626', marginBottom: 10 }}>Already Voted</Text>
              <Text style={{ color: '#1E293B', fontSize: 15, marginBottom: 18, textAlign: 'center' }}>
                You have already cast your vote for {alreadyVotedElection}. You can only vote once per election.
              </Text>
              <TouchableOpacity 
                onPress={() => setShowAlreadyVotedModal(false)} 
                style={{ 
                  backgroundColor: '#2563EB', 
                  borderRadius: 8, 
                  paddingVertical: 10, 
                  paddingHorizontal: 32,
                  shadowColor: '#2563EB',
                  shadowOffset: { width: 0, height: 2 },
                  shadowOpacity: 0.15,
                  shadowRadius: 4,
                  elevation: 2
                }}
              >
                <Text style={{ color: '#fff', fontWeight: 'bold', fontSize: 16 }}>Okay</Text>
              </TouchableOpacity>
            </View>
          </View>
        </Modal>

        {/* Coming Soon Modal */}
        <Modal
          visible={showComingSoonModal}
          transparent
          animationType="fade"
          onRequestClose={() => setShowComingSoonModal(false)}
        >
          <View style={{ flex: 1, backgroundColor: 'rgba(0,0,0,0.4)', justifyContent: 'center', alignItems: 'center' }}>
            <View style={{ backgroundColor: '#fff', borderRadius: 16, padding: 24, width: '85%', maxWidth: 400, alignItems: 'center' }}>
              <Text style={{ fontWeight: 'bold', fontSize: 20, color: '#F59E42', marginBottom: 10 }}>Coming Soon</Text>
              <Text style={{ color: '#1E293B', fontSize: 15, marginBottom: 18, textAlign: 'center' }}>This election has not started yet. Please check back later.</Text>
              <TouchableOpacity 
                onPress={() => setShowComingSoonModal(false)} 
                style={{ 
                  backgroundColor: '#2563EB', 
                  borderRadius: 8, 
                  paddingVertical: 10, 
                  paddingHorizontal: 32,
                  shadowColor: '#2563EB',
                  shadowOffset: { width: 0, height: 2 },
                  shadowOpacity: 0.15,
                  shadowRadius: 4,
                  elevation: 2
                }}
              >
                <Text style={{ color: '#fff', fontWeight: 'bold', fontSize: 16 }}>Okay</Text>
              </TouchableOpacity>
            </View>
          </View>
        </Modal>
      </ScrollView>
    </View>
  );
}

const styles = StyleSheet.create({
  container: {
    flex: 1,
    backgroundColor: '#f3f4f6',
  },
});
