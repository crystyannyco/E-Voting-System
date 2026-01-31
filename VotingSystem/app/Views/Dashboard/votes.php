<!-- Votes Content -->
<div class="p-6">
    <!-- Election Selection -->
    <div class="mb-6 bg-white rounded-lg shadow p-6 max-auto mx-auto">
        <h2 class="text-xl font-semibold text-blue-800 mb-4 border-b pb-2">Election Results</h2>

        <div class="mb-1">
            <label for="electionSelect" class="block text-gray-700 font-medium mb-2">Select Election:</label>
            <select id="electionSelect" class="w-full p-2 border rounded">
                <option value="">Select an Election</option>
                <?php foreach ($elections as $election): ?>
                    <option value="<?= $election['ElectionID'] ?>"><?= htmlspecialchars($election['ElectionName']) ?></option>
                <?php endforeach; ?>
            </select>
        </div>
    </div>

    
    <!-- Loading Indicator -->
    <div id="loadingIndicator" class="hidden text-center py-10">
        <div class="inline-block animate-spin rounded-full h-8 w-8 border-t-2 border-b-2 border-blue-600"></div>
        <p class="mt-2 text-gray-600">Loading election data...</p>
    </div>
    
    <!-- Election Data - initially hidden -->
    <div id="electionData" class="hidden">
        <!-- Election Info Container -->
        <div class="flex flex-col md:flex-row gap-6 mb-6">
            <!-- Left Section -->
            <div class="w-full md:w-1/2 bg-white rounded-lg shadow p-6">
                <h2 class="text-xl font-bold text-blue-800 mb-4" id="electionTitle">Election Title</h2>
                
                <div class="mb-4">
                    <div class="text-sm text-gray-600">Election Period:</div>
                    <div class="font-medium" id="electionPeriod">May 13-15, 2025 | Closes at 7:00 PM</div>
                </div>
                
                <div class="flex justify-between">
                    <div>
                        <span class="text-sm text-gray-600">Time Remaining:</span>
                        <div class="font-medium" id="timeRemaining">02 Days 05 Hours 30 Minutes</div>
                    </div>
                    <div>
                        <span class="text-sm text-gray-600">Status:</span>
                        <div class="font-medium" id="electionStatus">Ongoing</div>
                    </div>
                </div>
            </div>
            
            <!-- Right Section -->
            <div class="w-full md:w-1/2 bg-white rounded-lg shadow p-6">
                <h2 class="text-xl font-bold text-gray-800 mb-4">Votes</h2>
                
                <div class="relative pt-1 mb-4">
                    <div class="flex h-12 overflow-hidden text-white rounded-md">
                        <div id="votedPercentage" class="bg-blue-600 flex items-center justify-center text-xl font-bold">
                            75%
                        </div>
                        <div id="notVotedPercentage" class="bg-red-600 flex items-center justify-center text-xl font-bold">
                            25%
                        </div>
                    </div>
                </div>
                
                <!-- Voting Legend -->
                <div class="flex justify-between px-4 mt-4">
                    <div class="flex items-center">
                        <div class="w-4 h-4 bg-blue-600 rounded-full mr-2"></div>
                        <span class="text-gray-800 font-medium">ALREADY VOTED</span>
                    </div>
                    <div class="flex items-center">
                        <div class="w-4 h-4 bg-red-600 rounded-full mr-2"></div>
                        <span class="text-gray-800 font-medium">NOT YET VOTED</span>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Votes Tally Section -->
        <div class="flex justify-between items-center mb-4">
            <h2 class="text-xl font-bold text-gray-800">Votes Tally</h2>
            <!-- Print Button -->
            <div class="flex">
                <button id="printButton" class="flex items-center mr-2 bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 px-4 rounded-lg shadow-md">
                    <span class="material-icons mr-2">print</span>
                    Print
                </button>

                <button id="signatureButton" class="flex items-center bg-green-600 hover:bg-green-700 text-white font-medium py-2 px-4 rounded-lg shadow-md">
                    <span class="material-icons mr-2">add</span>
                    Signature
                </button>
            </div>
            
        </div>

        <!-- Positions Grid - Will be populated by JS -->
        <div id="positionsGrid" class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Position cards will be inserted here -->
        </div>
    </div>
    
    <!-- No Data Selected -->
    <div id="noDataSelected" class="bg-white rounded-lg shadow-md p-10 text-center">
        <div class="text-gray-400 mb-4">
            <span class="material-icons text-6xl">ballot</span>
        </div>
        <h3 class="text-xl font-medium text-gray-600">No Election Selected</h3>
        <p class="text-gray-500 mt-2">Please select an election from the dropdown above to view vote tallies.</p>
    </div>
</div>

<!-- JavaScript for Votes Page -->
<script>
    document.addEventListener('DOMContentLoaded', function() {
    const electionSelect = document.getElementById('electionSelect');
    const loadingIndicator = document.getElementById('loadingIndicator');
    const electionData = document.getElementById('electionData');
    const noDataSelected = document.getElementById('noDataSelected');
    const positionsGrid = document.getElementById('positionsGrid');
    const printButton = document.getElementById('printButton');
    
    // Election info elements
    const electionTitle = document.getElementById('electionTitle');
    const electionPeriod = document.getElementById('electionPeriod');
    const timeRemaining = document.getElementById('timeRemaining');
    const electionStatus = document.getElementById('electionStatus');
    const votedPercentage = document.getElementById('votedPercentage');
    const notVotedPercentage = document.getElementById('notVotedPercentage');

    // Add modal HTML to your page - moved this outside of the DOMContentLoaded event
    const modalHTML = `
    <div id="signatureModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 flex items-center justify-center hidden z-50">
        <div class="bg-white rounded-lg shadow-lg p-6 w-full max-w-md">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-lg font-semibold text-gray-800">Add Signature</h3>
                <button id="closeSignatureModal" class="text-gray-400 hover:text-gray-600">
                    <span class="material-icons">close</span>
                </button>
            </div>
            
            <div class="mb-4">
                <label for="signaturePosition" class="block text-sm font-medium text-gray-700 mb-1">Position:</label>
                <input type="text" id="signaturePosition" class="w-full rounded-md border border-gray-300 p-2 focus:ring focus:ring-blue-200 focus:border-blue-500">
            </div>
            
            <div class="mb-4">
                <label for="signatureOffice" class="block text-sm font-medium text-gray-700 mb-1">Office/Department:</label>
                <input type="text" id="signatureOffice" class="w-full rounded-md border border-gray-300 p-2 focus:ring focus:ring-blue-200 focus:border-blue-500">
            </div>
            
            <div class="flex justify-end">
                <button id="addSignatureBtn" class="bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 px-4 rounded-lg shadow-md">
                    Add Signature
                </button>
            </div>
        </div>
    </div>
    `;
    document.body.insertAdjacentHTML('beforeend', modalHTML);

    // Add signature list modal for managing/removing signatures
    const signatureListModalHTML = `
    <div id="signatureListModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 flex items-center justify-center hidden z-50">
        <div class="bg-white rounded-lg shadow-lg p-6 w-full max-w-md">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-lg font-semibold text-gray-800">Manage Signatures</h3>
                <button id="closeSignatureListModal" class="text-gray-400 hover:text-gray-600">
                    <span class="material-icons">close</span>
                </button>
            </div>
            
            <div id="signatureList" class="max-h-60 overflow-y-auto">
                <!-- Signatures will be listed here -->
            </div>
            
            <div class="flex justify-between mt-4">
                <button id="closeSignatureListBtn" class="bg-gray-500 hover:bg-gray-600 text-white font-medium py-2 px-4 rounded-lg shadow-md">
                    Close
                </button>
                <button id="addNewSignatureBtn" class="bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 px-4 rounded-lg shadow-md">
                    Add New
                </button>
            </div>
        </div>
    </div>
    `;
    document.body.insertAdjacentHTML('beforeend', signatureListModalHTML);

    const signatureButton = document.getElementById('signatureButton');
    const signatureModal = document.getElementById('signatureModal');
    const closeSignatureModal = document.getElementById('closeSignatureModal');
    const addSignatureBtn = document.getElementById('addSignatureBtn');
    const signaturePosition = document.getElementById('signaturePosition');
    const signatureOffice = document.getElementById('signatureOffice');
    
    const signatureListModal = document.getElementById('signatureListModal');
    const closeSignatureListModal = document.getElementById('closeSignatureListModal');
    const closeSignatureListBtn = document.getElementById('closeSignatureListBtn');
    const addNewSignatureBtn = document.getElementById('addNewSignatureBtn');
    const signatureList = document.getElementById('signatureList');

    // Open signature management modal when signature button is clicked
    signatureButton.addEventListener('click', function() {
        updateSignatureList();
        signatureListModal.classList.remove('hidden');
    });

    // Close modal when close button is clicked
    closeSignatureListModal.addEventListener('click', function() {
        signatureListModal.classList.add('hidden');
    });

    // Close modal with close button
    closeSignatureListBtn.addEventListener('click', function() {
        signatureListModal.classList.add('hidden');
    });

    // Open add signature modal
    addNewSignatureBtn.addEventListener('click', function() {
        signatureListModal.classList.add('hidden');
        signatureModal.classList.remove('hidden');
    });

    // Close modal when clicking outside the modal content
    signatureListModal.addEventListener('click', function(e) {
        if (e.target === signatureListModal) {
            signatureListModal.classList.add('hidden');
        }
    });

    // Close modal when close button is clicked
    closeSignatureModal.addEventListener('click', function() {
        signatureModal.classList.add('hidden');
    });

    // Close modal when clicking outside the modal content
    signatureModal.addEventListener('click', function(e) {
        if (e.target === signatureModal) {
            signatureModal.classList.add('hidden');
        }
    });
    
    // Variable to store fetched election data globally
    let currentElectionData = null;
    
    // Update the signature list in the modal
    function updateSignatureList() {
        const signatures = JSON.parse(localStorage.getItem('certificateSignatures') || '[]');
        signatureList.innerHTML = '';
        
        if (signatures.length === 0) {
            signatureList.innerHTML = '<p class="text-gray-500 text-center py-4">No signatures added yet</p>';
            return;
        }
        
        signatures.forEach((sig, index) => {
            const item = document.createElement('div');
            item.className = 'border-b border-gray-200 py-3 px-2 flex justify-between items-center';
            item.innerHTML = `
                <div>
                    <div class="font-medium">${sig.position}</div>
                    <div class="text-sm text-gray-600">${sig.office}</div>
                </div>
                <button class="remove-signature-btn text-red-600 hover:text-red-800" data-index="${index}">
                    <span class="material-icons">delete</span>
                </button>
            `;
            signatureList.appendChild(item);
        });
        
        // Attach event listeners to remove buttons
        document.querySelectorAll('.remove-signature-btn').forEach(btn => {
            btn.addEventListener('click', function() {
                const index = parseInt(this.getAttribute('data-index'));
                removeSignature(index);
            });
        });
    }
    
    // Remove a signature by index
    function removeSignature(index) {
        const signatures = JSON.parse(localStorage.getItem('certificateSignatures') || '[]');
        signatures.splice(index, 1);
        localStorage.setItem('certificateSignatures', JSON.stringify(signatures));
        updateSignatureList();
    }
    
    electionSelect.addEventListener('change', function() {
        const electionId = this.value;
        
        if (!electionId) {
            // No election selected
            electionData.classList.add('hidden');
            noDataSelected.classList.remove('hidden');
            return;
        }
        
        // Show loading indicator
        loadingIndicator.classList.remove('hidden');
        electionData.classList.add('hidden');
        noDataSelected.classList.add('hidden');
        
        // Fetch election data
        // After fetching election data:
        fetch(`${window.location.origin}/dashboard/getElectionVotes/${electionId}`)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Store election data globally
                currentElectionData = data;
                
                // Update election info
                electionTitle.textContent = data.election.ElectionName;

                // --- Update top bar title ---
                sessionStorage.setItem('pageTitle', data.election.ElectionName);
                if (document.getElementById('overviewText')) {
                    document.getElementById('overviewText').textContent = data.election.ElectionName;
                }
                
                // Format dates
                const startDate = new Date(data.election.Start);
                const endDate = new Date(data.election.End);
                const startFormatted = startDate.toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' });
                const endFormatted = endDate.toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' });
                const endTime = endDate.toLocaleTimeString('en-US', { hour: 'numeric', minute: 'numeric', hour12: true });
                
                electionPeriod.textContent = `${startFormatted} - ${endFormatted} | Closes at ${endTime}`;
                timeRemaining.textContent = data.timeInfo.timeRemaining;
                electionStatus.textContent = data.timeInfo.status;
                electionStatus.className = `font-medium text-${data.timeInfo.statusColor}-600`;
                
                // Update voting percentages - these are based on total eligible students vs voted students
                votedPercentage.textContent = data.votingStats.votedPercentage > 0 ? `${data.votingStats.votedPercentage}%` : '';
                votedPercentage.style.width = `${data.votingStats.votedPercentage}%`;
                notVotedPercentage.textContent = data.votingStats.notVotedPercentage > 0 ? `${data.votingStats.notVotedPercentage}%` : '';
                notVotedPercentage.style.width = `${data.votingStats.notVotedPercentage}%`;
                
                // Build position cards
                positionsGrid.innerHTML = '';
                
                for (const [positionId, positionData] of Object.entries(data.candidateStats)) {
                    const positionName = data.positions[positionId];
                    const positionCard = document.createElement('div');
                    positionCard.className = 'bg-white rounded-lg shadow-md p-6';
                    
                    // Position header with voting info
                    const positionHeader = document.createElement('div');
                    positionHeader.className = 'flex justify-between items-center mb-4';
                    positionHeader.innerHTML = `
                        <h3 class="text-lg font-semibold">${positionName}</h3>
                        <div class="text-sm text-gray-500">
                            ${positionData.totalVotes} out of ${positionData.eligibleVoters} voted 
                            (${Math.round((positionData.totalVotes / positionData.eligibleVoters) * 100)}%)
                        </div>
                    `;
                    positionCard.appendChild(positionHeader);
                    
                    // Sort candidates by votes (highest first)
                    const sortedCandidates = [...positionData.candidates].sort((a, b) => b.votes - a.votes);
                    
                    // If there are no candidates at all, show a message
                    if (!sortedCandidates.length) {
                        const noTallyMsg = document.createElement('div');
                        noTallyMsg.className = 'text-center text-gray-500 italic my-4';
                        noTallyMsg.textContent = 'No vote tally available for this position.';
                        positionCard.appendChild(noTallyMsg);
                        positionsGrid.appendChild(positionCard);
                        continue;
                    }
                    
                    // Add candidates
                    sortedCandidates.forEach(candidate => {
                        const candidateItem = document.createElement('div');
                        candidateItem.className = 'mb-4';
                        
                        const isAbstain = candidate.isAbstain || false;
                        const name = isAbstain ? 'Abstain' : `${candidate.FirstName} ${candidate.LastName}`;
                        const barColor = isAbstain ? 'bg-red-600' : 'bg-blue-600';
                        
                        candidateItem.innerHTML = `
                            <div class="flex justify-between mb-1">
                                <span class="font-medium text-sm">${name}</span>
                                <span class="font-medium text-sm">${candidate.votes} (${candidate.percentage}%)</span>
                            </div>
                            <div class="w-full bg-gray-200 rounded-full h-2">
                                <div class="${barColor} h-2 rounded-full" style="width: ${candidate.percentage}%"></div>
                            </div>
                        `;
                        
                        positionCard.appendChild(candidateItem);
                    });
                    
                    positionsGrid.appendChild(positionCard);
                }
                
                // Show election data container
                electionData.classList.remove('hidden');
            } else {
                // Show error message
                alert('Failed to load election data: ' + data.message);
                noDataSelected.classList.remove('hidden');
            }
        })

        .catch(error => {
            console.error('Error:', error);
            alert('An error occurred while loading election data.');
            noDataSelected.classList.remove('hidden');
        })
        
        .finally(() => {
            // Hide loading indicator
            loadingIndicator.classList.add('hidden');
        });
    });

    // --- AUTO-SELECT ELECTION BASED ON URL PARAM ---
    function getQueryParam(name) {
        const url = new URL(window.location.href);
        return url.searchParams.get(name);
    }

    // On page load, check for election_id in URL and select it
    const electionIdFromUrl = getQueryParam('election_id');
    if (electionIdFromUrl) {
        // Wait for DOM to be ready and select to be populated
        setTimeout(function() {
            if (electionSelect) {
                electionSelect.value = electionIdFromUrl;
                // Trigger change event to load election data
                const event = new Event('change', { bubbles: true });
                electionSelect.dispatchEvent(event);
            }
        }, 100);
    }

    // Print button functionality
    printButton.addEventListener('click', function() {
        if (!currentElectionData) {
            alert('Please select an election first.');
            return;
        }
        
        // Generate certificate HTML
        const certificateHTML = generateCertificateHTML(currentElectionData);
        
        // Create a hidden iframe for printing
        const iframe = document.createElement('iframe');
        iframe.style.display = 'none';
        document.body.appendChild(iframe);
        
        // Write the certificate HTML to the iframe
        iframe.contentDocument.write(certificateHTML);
        iframe.contentDocument.close();
        
        // Wait for resources to load then print
        setTimeout(() => {
            iframe.contentWindow.print();
            
            // Remove the iframe after printing
            setTimeout(() => {
                document.body.removeChild(iframe);
            }, 1000);
        }, 500);
    });

    // Handle adding a new signature
    addSignatureBtn.addEventListener('click', function() {
        const position = signaturePosition.value.trim();
        const office = signatureOffice.value.trim();
        
        if (!position || !office) {
            alert('Please fill in all fields.');
            return;
        }
        
        // Store the signature in localStorage for persistence
        const signatures = JSON.parse(localStorage.getItem('certificateSignatures') || '[]');
        signatures.push({ position, office });
        localStorage.setItem('certificateSignatures', JSON.stringify(signatures));
        
        // Clear form fields
        signaturePosition.value = '';
        signatureOffice.value = '';
        
        // Close the modal
        signatureModal.classList.add('hidden');
        
        // Show the list modal with updated list
        updateSignatureList();
        signatureListModal.classList.remove('hidden');
        
        // Alert success
        alert('Signature added successfully. It will appear in the printed certificate.');
    });
    
    // Function to generate certificate HTML
    function generateCertificateHTML(data) {
        const election = data.election;
        const departmentName = getDepartmentName(election.Department);
        const positions = data.positions;
        const candidatesByPosition = data.candidateStats;
        const votingStats = data.votingStats;
        const dateInfo = data.dateInfo;
        
        // Get current date and time in Philippine format
        const now = new Date();
        const options = { 
            year: 'numeric', 
            month: 'long', 
            day: 'numeric',
            hour: 'numeric',
            minute: '2-digit',
            second: '2-digit',
            hour12: true
        };
        const generatedDateTime = now.toLocaleDateString('en-US', options);

        // Get stored signatures
        const customSignatures = JSON.parse(localStorage.getItem('certificateSignatures') || '[]');

        // Create header HTML function for reuse
        function getHeaderHTML() {
            return `
            <div class="header-section">
                <div class="logo-container">
                    <img class="logo" src="${window.location.origin}/assets/cspc_logo.png" alt="CSPC Logo">
                    <div class="header">
                        <h1>Republic of the Philippines</h1>
                        <h2>Camarines Sur Polytechnic Colleges</h2>
                        <h1>Nabua, Camarines Sur</h1>
                        <h4><i>Academic Year 2024-2025</i></h4>
                        <h2>SUPREME STUDENT COUNCIL</h2>
                    </div>
                    <img class="logo" src="${window.location.origin}/assets/SSC Logo.png" alt="SSC Logo">
                </div>
                <div class="divider"></div>
            </div>
            `;
        }

        // Start building HTML for certificate
        let html = `
        <!DOCTYPE html>
        <html lang="en">
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>${election.ElectionName} - Official Results</title>
            <style>
                @page {
                    size: 8.5in 13in;
                    margin: 0.5in;
                }
                
                body {
                    font-family: Arial, sans-serif;
                    width: 8.5in;
                    height: 13in;
                    margin: 0 auto;
                    padding: 0.5in;
                    color: #000;
                    border: 1px solid black;
                    box-sizing: border-box;
                }
                
                .header {
                    text-align: center;
                    margin-bottom: 6px;
                }
                
                .header h1, .header h2, .header h4 {
                    font-family: 'Times New Roman', Times, serif;
                    margin: 5px 0;
                    line-height: 12px;
                }
                
                .header h1 {
                    font-size: 18px;
                    font-weight: normal;
                }
                
                .header h2 {
                    font-size: 20px;
                    font-weight: bold;
                }
                
                .header h4 {
                    font-size: 14px;
                    font-weight: normal;
                }
                
                .logo-container {
                    display: flex;
                    justify-content: space-between;
                    margin-bottom: 10px;
                }
                
                .logo {
                    width: 80px;
                    height: 80px;
                    margin-top: 10px;
                }
                
                .divider {
                    text-align: center;
                    margin: 10px 0;
                    border-top: 4px solid #000;
                }
                
                .title {
                    text-align: center;
                    font-family: 'Times New Roman', Times, serif;
                    font-weight: bold;
                    font-size: 22px;
                    margin-bottom: 0px;
                }
                
                .subtitle {
                    text-align: center;
                    font-family: 'Times New Roman', Times, serif;
                    font-style: italic;
                    font-size: 18px;
                    margin-bottom: 0px;
                }
                
                .section-title {
                    text-align: center;
                    font-weight: bold;
                    font-size: 18px;
                    margin: 10px 0;
                    background-color: #f2f2f2;
                    padding: 8px;
                    border-top: 2px solid #000;
                    border-bottom: 2px solid #000;
                }
                
                table {
                    width: 100%;
                    border-collapse: collapse;
                    margin-bottom: 20px;
                }
                
                th, td {
                    border: 1px solid #000;
                    padding: 4px 6px;
                    text-align: center;
                    font-size: 13px;
                }
                
                th {
                    background-color: #f2f2f2;
                    font-weight: bold;
                }
                
                .winner {
                    background-color: #d6ebf2;
                }
                
                .abstain {
                    background-color: #f9d6d6;
                }
                
                .summary-table td:first-child {
                    text-align: left;
                }
                
                .certification {
                    margin-top: 20px;
                    text-align: center;
                }
                
                .certification p {
                    text-align: justify;
                    font-family: 'Times New Roman', Times, serif;
                    line-height: 1.5;
                }
                
                .signatures {
                    display: flex;
                    flex-wrap: wrap;
                    justify-content: space-between;
                    margin-top: 30px;
                    gap: 20px;
                }
                
                .signature {
                    text-align: center;
                    width: 30%;
                    margin-bottom: 40px;
                }
                
                .signature-line {
                    border-top: 1px solid #000;
                    margin-bottom: 5px;
                }
                
                .footnote {
                    font-size: 12px;
                    text-align: right;
                    font-style: italic;
                    margin-top: 20px;
                }
                
                .page-break {
                    page-break-before: always;
                }

                /* Critical CSS for proper headers on all pages */
                @media print {
                    .header-section {
                        display: block;
                    }
                    
                    .certification-container {
                        page-break-before: auto;
                        page-break-inside: avoid;
                    }
                    
                    /* Ensure each page has a header if content breaks */
                    .content-section {
                        page-break-after: auto;
                    }
                    
                    /* Force header to top of each page */
                    .header-section {
                        position: running(header);
                    }
                    
                    @page {
                        @top-center {
                            content: element(header);
                        }
                    }
                    
                    body {
                        width: 100%;
                        height: 100%;
                        margin: 0;
                        padding: 0.5in;
                        border: none;
                    }
                }
            </style>
        </head>
        <body>
            <!-- First page header -->
            ${getHeaderHTML()}
            
            <!-- Main content starts here -->
            <div class="content-section">
                <div class="title">OFFICIAL E-VOTING RESULTS</div>
                <div class="subtitle">${departmentName}</div>
                <div class="subtitle">${election.ElectionName}</div>
                
                <div class="section-title">ELECTION RESULTS</div>
        `;
        
        // Create table for election results with additional columns for eligible voters and turnout
        html += `
            <table>
                <thead>
                    <tr>
                        <th>POSITION</th>
                        <th>CANDIDATE NAME</th>
                        <th>VOTES</th>
                        <th>ELIGIBLE VOTERS</th>
                        <th>PERCENTAGE</th>
                    </tr>
                </thead>
                <tbody>
        `;
        
        // Calculate estimated number of rows per page based on paper size
        // For 8.5x13 inch paper with margins, we can fit approximately 25-30 rows with the additional column
        const estimatedRowsPerPage = 25;
        let rowCount = 0;
        
        // Add candidates by position
        for (const [positionId, positionData] of Object.entries(candidatesByPosition)) {
            const positionName = positions[positionId];
            const rowSpan = positionData.candidates.length;
            const eligibleVoters = positionData.eligibleVoters;
            
            // Sort candidates by votes (highest first)
            const sortedCandidates = [...positionData.candidates].sort((a, b) => b.votes - a.votes);
            
            // Determine the winner (highest votes)
            const winner = sortedCandidates.find(c => !c.isAbstain) || sortedCandidates[0];
            
            // Check if we need a page break before this position
            if (rowCount > 0 && (rowCount + rowSpan) > estimatedRowsPerPage) {
                html += `
                    </tbody>
                    </table>
                    <!-- Insert page break with properly structured header -->
                    <div class="page-break"></div>
                    ${getHeaderHTML()}
                    <div class="section-title">ELECTION RESULTS (Continued)</div>
                    <table>
                        <thead>
                            <tr>
                                <th>POSITION</th>
                                <th>CANDIDATE NAME</th>
                                <th>VOTES</th>
                                <th>ELIGIBLE VOTERS</th>
                                <th>PERCENTAGE</th>
                            </tr>
                        </thead>
                        <tbody>
                `;
                rowCount = 0; // Reset row count for new page
            }
            
            // Add rows for each candidate
            sortedCandidates.forEach((candidate, index) => {
                const isWinner = candidate.votes === winner.votes && !candidate.isAbstain;
                const isAbstain = candidate.isAbstain || false;
                const name = isAbstain ? 'Abstain' : `${candidate.FirstName} ${candidate.MiddleName ? candidate.MiddleName.charAt(0) + '. ' : ''}${candidate.LastName}`;
                
                html += `
                    <tr ${isWinner ? 'class="winner"' : isAbstain ? 'class="abstain"' : ''}>
                        ${index === 0 ? `<td rowspan="${rowSpan}">${positionName}</td>` : ''}
                        <td>${name}</td>
                        <td>${candidate.votes}</td>
                        <td>${eligibleVoters}</td>
                        <td>${candidate.percentage}%</td>
                    </tr>
                `;
            });
            
            rowCount += rowSpan; // Increment row count by the number of candidates in this position
        }
        
        html += `
                </tbody>
            </table>
            
            <div class="section-title">ELECTION SUMMARY</div>
            
            <table class="summary-table">
                <tr>
                    <td>Total Number of Registered Voters</td>
                    <td>${votingStats.totalStudents}</td>
                </tr>
                <tr>
                    <td>Total Number of Votes Cast</td>
                    <td>${votingStats.votedStudents}</td>
                </tr>
                <tr>
                    <td>Voter Turnout</td>
                    <td>${votingStats.votedPercentage}%</td>
                </tr>
                <tr>
                    <td>Start of Election</td>
                    <td>${dateInfo.startDate}</td>
                </tr>
                <tr>
                    <td>End of Election</td>
                    <td>${dateInfo.endDate}</td>
                </tr>
            </table>
            
            <div class="footnote">Generated: ${generatedDateTime}</div>
        </div>
            
        <!-- Always force a page break before certification to ensure clean layout -->
        <div class="page-break"></div>
        ${getHeaderHTML()}
        
        <!-- Certification container -->
        <div class="certification-container">
            <div class="certification">
                <br>
                <h3 style="font-family: 'Times New Roman', Times, serif;">CERTIFICATION</h3>
                <p>We hereby certify that the above results are true and correct based on the Electronic Voting System used for the ${election.ElectionName} of the ${departmentName} at Camarines Sur Polytechnic Colleges.</p><br>
            </div>
            
            <div class="signatures">
                <!-- Default signatures -->
                <div class="signature">
                    <div class="signature-line"></div>
                    <strong>Adviser</strong><br>
                    Supreme Student Council
                </div>
                <div class="signature">
                    <div class="signature-line"></div>
                    <strong>Dean</strong><br>
                    ${departmentName}
                </div>
                <div class="signature">
                    <div class="signature-line"></div>
                    <strong>Director</strong><br>
                    Student Affairs Office
                </div>
                
                <!-- Add custom signatures in rows of 3 -->
                ${customSignatures.map((sig, index) => `
                    ${index % 3 === 0 && index !== 0 ? '<div style="width: 100%; height: 0;"></div>' : ''}
                    <div class="signature">
                        <div class="signature-line"></div>
                        <strong>${sig.position}</strong><br>
                        ${sig.office}
                    </div>
                `).join('')}
            </div>
        </div>
        </body>
        </html>
        `;
        
        return html;
    }

    // Helper function to get department name from department ID
    function getDepartmentName(departmentId) {
        // More complete department mapping
        const departments = {
            '0': 'All Departments',
            '1': 'College of Computer Studies',
            '2': 'College of Engineering and Architecture',
            '3': 'College of Health Sciences',
            '4': 'College of Tourism, Hospitality and Business Management',
            '5': 'College of Technological and Developmental Education',
            '6': 'College of Arts and Sciences'
        };
        
        // Convert to string to ensure lookup works properly
        const deptId = departmentId.toString();
        return departments[deptId] || 'Unknown Department';
    }
});
</script>