import React, { useEffect, useState } from 'react';
import { View, Text, StyleSheet, ScrollView, TouchableOpacity, ActivityIndicator, Image, Modal } from 'react-native';
import { useLocalSearchParams, router } from 'expo-router';
import AsyncStorage from '@react-native-async-storage/async-storage';

// Ballot radio button component
function RadioButton({ selected, onPress, label, avatarUri }: { selected: boolean; onPress: () => void; label: React.ReactNode; avatarUri?: string }) {
  return (
    <TouchableOpacity onPress={onPress} style={styles.radioRow} activeOpacity={0.8}>
      <View style={[styles.radioOuter, selected && styles.radioOuterSelected]}>
        {selected ? <View style={styles.radioInner} /> : null}
      </View>
      {avatarUri ? (
        typeof avatarUri === 'string' ? (
          !!avatarUri ? <Image source={{ uri: avatarUri }} style={styles.candidateAvatar} /> : null
        ) : (
          <Image source={avatarUri} style={styles.candidateAvatar} />
        )
      ) : null}
      <View style={{ flex: 1 }}>{label}</View>
    </TouchableOpacity>
  );
}

export default function CastVote() {
  const { electionId, electionTitle, electionStart, electionEnd, electionStatus } = useLocalSearchParams();
  const [loading, setLoading] = useState(true);
  const [ballot, setBallot] = useState<any[]>([]); // [{ position, candidates: [], positionName }]
  const [selected, setSelected] = useState<Record<string, string>>({}); // { positionId: candidateId | 'abstain' }
  const [error, setError,] = useState<string | null>(null);
  const [studentId, setStudentId] = useState<number | null>(null);
  const [missingVotes, setMissingVotes] = useState<Record<string, boolean>>({});
  const [showConfirmModal, setShowConfirmModal] = useState(false);
  const [previewVotes, setPreviewVotes] = useState<string[]>([]);
  const [showErrorModal, setShowErrorModal] = useState(false);
  const [errorModalMessage, setErrorModalMessage] = useState<string>('');
  const [showPreviewModal, setShowPreviewModal] = useState(false);
  const [showSuccessModal, setShowSuccessModal] = useState(false);

  // Helper to format date range
  function formatDateRange(start: string, end: string): string {
    const startDate = new Date(start);
    const endDate = new Date(end);
    const options: Intl.DateTimeFormatOptions = { year: 'numeric', month: 'short', day: 'numeric', hour: '2-digit', minute: '2-digit' };
    return `${startDate.toLocaleString(undefined, options)} - ${endDate.toLocaleString(undefined, options)}`;
  }

  // Helper to get remaining time
  function getTimeRemaining(end: string): string {
    const now = new Date();
    const endDate = new Date(end);
    const diff = endDate.getTime() - now.getTime();
    if (diff <= 0) return 'Election ended';
    const days = Math.floor(diff / (1000 * 60 * 60 * 24));
    const hours = Math.floor((diff / (1000 * 60 * 60)) % 24);
    const minutes = Math.floor((diff / (1000 * 60)) % 60);
    return `${days}d ${hours}h ${minutes}m remaining`;
  }

  // Helper to build the payload for the API
  function buildVotePayload() {
    // selected: { positionId: candidateId | 'abstain' }
    // Map ballot to ensure order and correct mapping
    const votes = ballot.map(({ position, candidates }) => {
      const candidateId = selected[position];
      let positionId = null;
      if (candidateId === 'abstain') {
        // Use the position as is for abstain
        positionId = parseInt(position, 10);
      } else {
        // Find the candidate and get their Position (should be numeric)
        const candidate = candidates.find((c: any) => c.CandidateID.toString() === candidateId);
        positionId = candidate ? Number(candidate.Position) : parseInt(position, 10);
      }
      return {
        CandidateID: candidateId === 'abstain' ? 0 : parseInt(candidateId, 10),
        PositionID: positionId
      };
    });
    return {
      StudentID: studentId,
      ElectionID: electionId,
      votes,
    };
  }

  // Handler for confirming and submitting the vote
  async function handleFinalVote() {
    setShowConfirmModal(false);
    setError(null);
    try {
      const payload = buildVotePayload();
      console.log('Cast Vote Payload:', payload); // Debug: show payload in console
      const response = await fetch('http://172.16.116.113:8080/api/cast-vote', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify(payload),
      });
      const data = await response.json();
      if (data.meta && data.meta.code === 200) {
        setShowSuccessModal(true);
      } else {
        setErrorModalMessage(data.meta?.message || 'Failed to cast vote.');
        setShowErrorModal(true);
      }
    } catch (e) {
      setErrorModalMessage('Failed to cast vote.');
      setShowErrorModal(true);
    }
  }

  // Handler for previewing the ballot
  function handlePreviewBallot() {
    const votes = ballot.map(({ position, positionName, candidates }) => {
      const candidateId = selected[position];
      if (!candidateId) {
        return `${positionName}: Not selected`;
      }
      if (candidateId === 'abstain') {
        return `${positionName}: Abstain`;
      }
      const candidate = candidates.find((c: any) => c.CandidateID.toString() === candidateId);
      if (candidate) {
        return `${positionName}: ${candidate.FirstName} ${candidate.MiddleName} ${candidate.LastName}`;
      }
      return `${positionName}: Invalid selection`;
    });
    setPreviewVotes(votes);
    setShowPreviewModal(true);
  }

  function handleConfirmClick() {
    // Validation: check if all positions have a selection
    const missing: Record<string, boolean> = {};
    ballot.forEach(({ position }) => {
      if (!selected[position]) {
        missing[position] = true;
      }
    });
    setMissingVotes(missing);
    if (Object.keys(missing).length > 0) {
      setErrorModalMessage('Please vote or abstain for all positions.');
      setShowErrorModal(true);
      return;
    }
    // Prepare preview for modal
    const votes = ballot.map(({ position, positionName, candidates }) => {
      const candidateId = selected[position];
      if (!candidateId) {
        return `${positionName}: Not selected`;
      }
      if (candidateId === 'abstain') {
        return `${positionName}: Abstain`;
      }
      const candidate = candidates.find((c: any) => c.CandidateID.toString() === candidateId);
      if (candidate) {
        return `${positionName}: ${candidate.FirstName} ${candidate.MiddleName} ${candidate.LastName}`;
      }
      return `${positionName}: Invalid selection`;
    });
    setPreviewVotes(votes);
    setShowConfirmModal(true);
  }

  useEffect(() => {
    if (!electionId) return;
    setLoading(true);
    fetch(`http://172.16.116.113:8080/api/election-info/${electionId}`)
      .then(res => res.json())
      .then(data => {
        if (data && data.data && data.data.candidates) {
          // Group by position
          const grouped: Record<string, any> = {};
          data.data.candidates.forEach((c: any) => {
            if (!grouped[c.Position]) grouped[c.Position] = { positionName: c.PositionName, candidates: [] };
            grouped[c.Position].candidates.push(c);
          });
          setBallot(Object.entries(grouped).map(([position, { positionName, candidates }]) => ({ position, positionName, candidates })));
        } else {
          setBallot([]);
        }
      })
      .catch(() => setError('Failed to load ballot.'))
      .finally(() => setLoading(false));
  }, [electionId]);

  useEffect(() => {
    // Fetch logged-in student ID from AsyncStorage
    const fetchStudentId = async () => {
      try {
        const userData = await AsyncStorage.getItem('userData');
        if (userData) {
          const parsed = JSON.parse(userData);
          setStudentId(Number(parsed.StudentID));
        }
      } catch (e) {
        setStudentId(null);
      }
    };
    fetchStudentId();
  }, []);

  const handleSelect = (position: string, candidateId: string) => {
    setSelected((prev) => ({ ...prev, [position]: candidateId }));
  };

  if (loading) {
    return <View style={styles.centered}><ActivityIndicator size="large" color="#2563EB" /></View>;
  }
  if (!ballot.length) {
    return <View style={styles.centered}><Text style={{ color: '#64748B', fontSize: 16 }}>No ballot available for this election.</Text></View>;
  }

  return (
    <ScrollView style={styles.container} contentContainerStyle={{ padding: 24, paddingBottom: 40 }}>
      {/* Debug: Show student number */}
      {/* <Text style={{ color: '#2563EB', fontWeight: 'bold', marginBottom: 8 }}>Student Number: {studentId ?? 'Loading...'}</Text> */}
      {/* Election Info Section */}
      {electionTitle && electionStart && electionEnd && (
        <View style={{ backgroundColor: '#fff', borderRadius: 16, padding: 18, marginBottom: 18, borderWidth: 1, borderColor: '#E5E7EB', elevation: 2, shadowColor: '#000', shadowOpacity: 0.07, shadowRadius: 8, shadowOffset: { width: 0, height: 2 } }}>
          <Text style={{ fontWeight: 'bold', fontSize: 20, color: '#1E293B', marginBottom: 4 }}>{electionTitle}</Text>
          <Text style={{ fontSize: 13, color: '#64748B', marginBottom: 2 }}>{formatDateRange(electionStart as string, electionEnd as string)}</Text>
          <Text style={{ color: '#2563EB', fontSize: 13, marginBottom: 2, fontWeight: 'bold' }}>{electionStatus}</Text>
          <Text style={{ color: '#64748B', fontSize: 13, marginBottom: 2 }}>{getTimeRemaining(electionEnd as string)}</Text>
        </View>
      )}
      <Text style={styles.title}>Cast Your Vote</Text>
      {ballot.map(({ position, positionName, candidates }) => (
        <View key={position} style={[styles.positionSection, missingVotes[position] && { borderColor: '#DC2626', borderWidth: 2 }]}>
          <Text style={styles.positionTitle}>{positionName}</Text>
          {candidates.map((c: any) => (
            <View key={c.CandidateID} style={{ flexDirection: 'row', alignItems: 'center', marginBottom: 10, backgroundColor: '#F8FAFC', borderRadius: 10, padding: 8, borderWidth: 1, borderColor: '#E5E7EB' }}>
              {/* Candidate Image */}
              {c.ProfileUrl ? (
                <Image source={{ uri: c.ProfileUrl }} style={{ width: 48, height: 48, borderRadius: 24, marginRight: 12, backgroundColor: '#E0E7FF' }} />
              ) : (
                <Image source={require('../../assets/images/default-profile.png')} style={{ width: 48, height: 48, borderRadius: 24, marginRight: 12, backgroundColor: '#E0E7FF' }} />
              )}
              <TouchableOpacity style={{ flex: 1 }} onPress={() => router.push({ pathname: '/(tabs)/candidate-info', params: { id: c.CandidateID } })}>
                {/* Name */}
                <Text style={{ fontWeight: 'bold', color: '#1E293B', fontSize: 16, marginBottom: 2 }}>
                  {c.FirstName} {c.MiddleName} {c.LastName}
                </Text>
                {/* Partylist */}
                {c.PartylistName ? (
                  <Text style={{ color: '#6366F1', fontSize: 13, fontWeight: '600', marginBottom: 2 }}>
                    {c.PartylistName}
                  </Text>
                ) : null}
              </TouchableOpacity>
              {/* RadioButton for selection */}
              <TouchableOpacity style={{ marginLeft: 8 }} onPress={() => handleSelect(position, c.CandidateID.toString())}>
                <RadioButton
                  selected={selected[position] === c.CandidateID.toString()}
                  onPress={() => handleSelect(position, c.CandidateID.toString())}
                  label={<Text />}
                  avatarUri={undefined}
                />
              </TouchableOpacity>
            </View>
          ))}
          {/* Abstain option styled like candidate */}
          <TouchableOpacity
            style={{ flexDirection: 'row', alignItems: 'center', marginBottom: 10, backgroundColor: '#F8FAFC', borderRadius: 10, padding: 8, borderWidth: 1, borderColor: '#E5E7EB' }}
            activeOpacity={1}
            disabled
          >
            <Image source={require('../../assets/images/default-profile.png')} style={{ width: 48, height: 48, borderRadius: 24, marginRight: 12, backgroundColor: '#E0E7FF' }} />
            <View style={{ flex: 1 }}>
              <Text style={{ fontWeight: 'bold', color: '#1E293B', fontSize: 16, marginBottom: 2 }}>Abstain</Text>
            </View>
            <View style={{ marginLeft: 8 }}>
              <RadioButton
                selected={selected[position] === 'abstain'}
                onPress={() => handleSelect(position, 'abstain')}
                label={<Text />}
                avatarUri={undefined}
              />
            </View>
          </TouchableOpacity>
        </View>
      ))}
      <View style={styles.buttonRow}>
        <TouchableOpacity style={styles.previewBtn} onPress={handlePreviewBallot}>
          <Text style={styles.previewBtnText}>Preview</Text>
        </TouchableOpacity>
        <TouchableOpacity style={styles.confirmBtn} onPress={handleConfirmClick}>
          <Text style={styles.confirmBtnText}>Confirm</Text>
        </TouchableOpacity>
      </View>
      {/* Confirm Modal */}
      <Modal
        visible={showConfirmModal}
        transparent
        animationType="fade"
        onRequestClose={() => setShowConfirmModal(false)}
      >
        <View style={{ flex: 1, backgroundColor: 'rgba(0,0,0,0.4)', justifyContent: 'center', alignItems: 'center' }}>
          <View style={{ backgroundColor: '#fff', borderRadius: 16, padding: 24, width: '85%', maxWidth: 400, alignItems: 'center' }}>
            <Text style={{ fontWeight: 'bold', fontSize: 20, color: '#1E293B', marginBottom: 10 }}>Preview Ballot</Text>
            <View style={{ alignSelf: 'stretch', marginBottom: 16 }}>
              {previewVotes.map((line, idx) => (
                <Text key={idx} style={{ color: '#1E293B', fontSize: 15, marginBottom: 2 }}>{line}</Text>
              ))}
            </View>
            <Text style={{ color: '#DC2626', fontSize: 14, marginBottom: 18, textAlign: 'center' }}>
              Are you sure you want to submit your vote? This action is <Text style={{ fontWeight: 'bold' }}>not reversible</Text>.
            </Text>
            <View style={{ flexDirection: 'row', gap: 12 }}>
              <TouchableOpacity onPress={() => setShowConfirmModal(false)} style={{ backgroundColor: '#E5E7EB', borderRadius: 8, paddingVertical: 10, paddingHorizontal: 24, marginRight: 8 }}>
                <Text style={{ color: '#1E293B', fontWeight: 'bold', fontSize: 16 }}>Cancel</Text>
              </TouchableOpacity>
              <TouchableOpacity onPress={handleFinalVote} style={{ backgroundColor: '#2563EB', borderRadius: 8, paddingVertical: 10, paddingHorizontal: 24 }}>
                <Text style={{ color: '#fff', fontWeight: 'bold', fontSize: 16 }}>Submit Vote</Text>
              </TouchableOpacity>
            </View>
          </View>
        </View>
      </Modal>
      {/* Error Modal for missing votes */}
      <Modal
        visible={showErrorModal}
        transparent
        animationType="fade"
        onRequestClose={() => setShowErrorModal(false)}
      >
        <View style={{ flex: 1, backgroundColor: 'rgba(0,0,0,0.4)', justifyContent: 'center', alignItems: 'center' }}>
          <View style={{ backgroundColor: '#fff', borderRadius: 16, padding: 24, width: '85%', maxWidth: 400, alignItems: 'center' }}>
            <Text style={{ fontWeight: 'bold', fontSize: 20, color: '#DC2626', marginBottom: 10 }}>Incomplete Ballot</Text>
            <Text style={{ color: '#1E293B', fontSize: 15, marginBottom: 18, textAlign: 'center' }}>{errorModalMessage}</Text>
            <TouchableOpacity onPress={() => setShowErrorModal(false)} style={{ backgroundColor: '#2563EB', borderRadius: 8, paddingVertical: 10, paddingHorizontal: 32 }}>
              <Text style={{ color: '#fff', fontWeight: 'bold', fontSize: 16 }}>Close</Text>
            </TouchableOpacity>
          </View>
        </View>
      </Modal>
      {/* Preview Modal for ballot */}
      <Modal
        visible={showPreviewModal}
        transparent
        animationType="fade"
        onRequestClose={() => setShowPreviewModal(false)}
      >
        <View style={{ flex: 1, backgroundColor: 'rgba(0,0,0,0.4)', justifyContent: 'center', alignItems: 'center' }}>
          <View style={{ backgroundColor: '#fff', borderRadius: 16, padding: 24, width: '85%', maxWidth: 400, alignItems: 'center' }}>
            <Text style={{ fontWeight: 'bold', fontSize: 20, color: '#1E293B', marginBottom: 10 }}>Preview Ballot</Text>
            <View style={{ alignSelf: 'stretch', marginBottom: 16 }}>
              {previewVotes.map((line, idx) => (
                <Text key={idx} style={{ color: '#1E293B', fontSize: 15, marginBottom: 2 }}>{line}</Text>
              ))}
            </View>
            <TouchableOpacity onPress={() => setShowPreviewModal(false)} style={{ backgroundColor: '#2563EB', borderRadius: 8, paddingVertical: 10, paddingHorizontal: 32 }}>
              <Text style={{ color: '#fff', fontWeight: 'bold', fontSize: 16 }}>Close</Text>
            </TouchableOpacity>
          </View>
        </View>
      </Modal>
      {/* Success Modal after vote */}
      <Modal
        visible={showSuccessModal}
        transparent
        animationType="fade"
        onRequestClose={() => setShowSuccessModal(false)}
      >
        <View style={{ flex: 1, backgroundColor: 'rgba(0,0,0,0.4)', justifyContent: 'center', alignItems: 'center' }}>
          <View style={{ backgroundColor: '#fff', borderRadius: 16, padding: 24, width: '85%', maxWidth: 400, alignItems: 'center' }}>
            <Text style={{ fontWeight: 'bold', fontSize: 20, color: '#22C55E', marginBottom: 10 }}>Vote Submitted!</Text>
            <Text style={{ color: '#1E293B', fontSize: 15, marginBottom: 18, textAlign: 'center' }}>Your vote has been successfully submitted.</Text>
            <TouchableOpacity onPress={() => { setShowSuccessModal(false); router.replace('/(tabs)/votes'); }} style={{ backgroundColor: '#2563EB', borderRadius: 8, paddingVertical: 10, paddingHorizontal: 32 }}>
              <Text style={{ color: '#fff', fontWeight: 'bold', fontSize: 16 }}>Okay</Text>
            </TouchableOpacity>
          </View>
        </View>
      </Modal>
    </ScrollView>
  );
}

const styles = StyleSheet.create({
  container: {
    flex: 1,
    backgroundColor: '#f3f4f6',
  },
  centered: {
    flex: 1,
    justifyContent: 'center',
    alignItems: 'center',
    backgroundColor: '#f3f4f6',
  },
  title: {
    fontSize: 28,
    fontWeight: 'bold',
    color: '#1E293B',
    marginBottom: 18,
    letterSpacing: -1,
  },
  positionSection: {
    backgroundColor: '#fff',
    borderRadius: 16,
    padding: 18,
    marginBottom: 24,
    borderWidth: 1,
    borderColor: '#E5E7EB',
    elevation: 2,
    shadowColor: '#000',
    shadowOpacity: 0.07,
    shadowRadius: 8,
    shadowOffset: { width: 0, height: 2 },
  },
  positionTitle: {
    fontSize: 18,
    fontWeight: 'bold',
    color: '#000',
    marginBottom: 10,
  },
  radioRow: {
    flexDirection: 'row',
    alignItems: 'center',
    marginBottom: 10,
  },
  radioOuter: {
    width: 22,
    height: 22,
    borderRadius: 11,
    borderWidth: 2,
    borderColor: '#94A3B8',
    alignItems: 'center',
    justifyContent: 'center',
    marginRight: 12,
    backgroundColor: '#fff',
  },
  radioOuterSelected: {
    borderColor: '#2563EB',
  },
  radioInner: {
    width: 12,
    height: 12,
    borderRadius: 6,
    backgroundColor: '#2563EB',
  },
  radioLabel: {
    fontSize: 16,
    color: '#1E293B',
    flexShrink: 1,
  },
  candidateAvatar: {
    width: 32,
    height: 32,
    borderRadius: 16,
    marginRight: 10,
    backgroundColor: '#E0E7FF',
  },
  buttonRow: {
    flexDirection: 'row',
    justifyContent: 'space-between',
    marginTop: 18,
    gap: 16,
  },
  previewBtn: {
    flex: 1,
    backgroundColor: '#fff',
    borderWidth: 1,
    borderColor: '#2563EB',
    borderRadius: 10,
    paddingVertical: 14,
    alignItems: 'center',
    marginRight: 8,
  },
  previewBtnText: {
    color: '#2563EB',
    fontWeight: 'bold',
    fontSize: 16,
  },
  confirmBtn: {
    flex: 1,
    backgroundColor: '#2563EB',
    borderRadius: 10,
    paddingVertical: 14,
    alignItems: 'center',
    marginLeft: 8,
  },
  confirmBtnText: {
    color: '#fff',
    fontWeight: 'bold',
    fontSize: 16,
  },
});
