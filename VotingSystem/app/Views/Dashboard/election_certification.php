<style>
    /* Default styles */
    .no-print {
        display: block;
    }
    @media print {
        .no-print {
            display: none !important;
        }
        body {
            background-color: white;
        }
        .print-container {
            box-shadow: none;
            margin: 0;
            padding: 0.5cm;
        }
    }
    .compact-form label {
        font-size: 0.875rem;
        margin-bottom: 0.25rem;
    }
    .compact-form input, .compact-form select {
        padding: 0.375rem 0.5rem;
    }
    .compact-controls {
        position: sticky;
        top: 0;
        z-index: 10;
        background-color: white;
        padding: 8px 0;
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    }
    .times-new-roman {
        font-family: "Times New Roman", Times, serif;
    }
    .arial {
        font-family: Arial, Helvetica, sans-serif;
    }
</style>
</head>
<body class="bg-gray-100">
    <!-- Top Controls (No Print) - Simplified and compact -->
    <div class="top-controls no-print compact-controls">
        <div class="max-w-4xl mx-auto flex justify-between items-center px-4">
            <button onclick="window.location.href='<?= base_url('votes') ?>'" class="bg-gray-600 hover:bg-gray-700 text-white font-semibold py-1 px-3 rounded flex items-center text-sm">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
                Back
            </button>
            <button onclick="toggleEditForm()" class="bg-yellow-500 hover:bg-yellow-600 text-white font-semibold py-1 px-3 rounded text-sm mx-2">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 inline mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                </svg>
                Edit
            </button>
            <button onclick="window.print()" class="bg-blue-600 hover:bg-blue-700 text-white font-semibold py-1 px-3 rounded flex items-center text-sm">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                </svg>
                Print
            </button>
        </div>
    </div>

    <!-- Edit Form (hidden in print and initially collapsed) -->
    <div id="editFormContainer" class="max-w-4xl mx-auto bg-white shadow-md mb-6 p-4 no-print compact-form hidden">
        <div class="flex justify-between items-center mb-2">
            <h2 class="text-lg font-bold">Edit Certification</h2>
            <button onclick="toggleEditForm()" class="text-gray-500 hover:text-gray-700">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>

        <!-- Editable Top Fields -->
        <div class="mb-3 no-print">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                <div>
                    <label class="block text-sm font-medium text-gray-700">Academic Year:</label>
                    <input type="text" id="academicYear" value="<?= date('Y') . '-' . (date('Y') + 1) ?>" 
                           class="editable-field mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-500 text-sm">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Department:</label>
                    <select id="department" class="editable-field mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-500 text-sm">
                        <option value="College of Computer Studies" <?= $election['Department'] == 1 ? 'selected' : '' ?>>College of Computer Studies</option>
                        <option value="College of Engineering and Architecture" <?= $election['Department'] == 2 ? 'selected' : '' ?>>College of Engineering and Architecture</option>
                        <option value="College of Health Sciences" <?= $election['Department'] == 3 ? 'selected' : '' ?>>College of Health Sciences</option>
                        <option value="College of Tourism, Hospitality and Business Management" <?= $election['Department'] == 4 ? 'selected' : '' ?>>College of Tourism, Hospitality and Business Management</option>
                        <option value="College of Technological and Developmental Education" <?= $election['Department'] == 5 ? 'selected' : '' ?>>College of Technological and Developmental Education</option>
                        <option value="College of Arts and Sciences" <?= $election['Department'] == 6 ? 'selected' : '' ?>>College of Arts and Sciences</option>
                        <option value="Camarines Sur Polytechnic Colleges" <?= $election['Department'] == 0 ? 'selected' : '' ?>>Camarines Sur Polytechnic Colleges</option>
                    </select>
                </div>
            </div>
        </div>
        
        <form action="<?= base_url('dashboard/electionCertification/' . $election['ElectionID']) ?>" method="post" id="representativeForm">
            <div class="mb-3">
                <h3 class="text-sm font-semibold text-gray-700 mb-1">Representatives</h3>
                <div id="representativesContainer" class="space-y-2">
                    <?php foreach ($representatives as $index => $rep): ?>
                    <div class="representative-entry p-2 border rounded bg-gray-50">
                        <div class="grid grid-cols-3 gap-2">
                            <div>
                                <label class="block text-xs font-medium text-gray-700">Name</label>
                                <input type="text" name="representatives[<?= $index ?>][name]" value="<?= htmlspecialchars($rep['name']) ?>" 
                                       class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-500 text-sm py-1">
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-gray-700">Position</label>
                                <input type="text" name="representatives[<?= $index ?>][title]" value="<?= htmlspecialchars($rep['title']) ?>" 
                                       class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-500 text-sm py-1">
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-gray-700">Organization</label>
                                <input type="text" name="representatives[<?= $index ?>][organization]" value="<?= htmlspecialchars($rep['organization']) ?>" 
                                       class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-500 text-sm py-1">
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
            
            <div class="flex justify-between mt-3">
                <button type="button" id="addRepresentative" class="bg-green-500 hover:bg-green-600 text-white font-semibold py-1 px-3 rounded text-sm flex items-center">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    Add Representative
                </button>
                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-semibold py-1 px-3 rounded text-sm flex items-center">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                    </svg>
                    Update
                </button>
            </div>
        </form>
    </div>
    
    <!-- Main Content (Printable) -->
    <div class="print-container max-w-5xl mb-6 mx-auto bg-white shadow-md p-12 print:shadow-none">
        
        <!-- Header Logos and Title -->
        <div class="flex justify-between items-center mb-4">
            <div class="w-24">
                <img src="<?= base_url('assets\cspc_logo.png') ?>" alt="School Logo" class="w-full">
            </div>
            <div class="text-center times-new-roman">
                <p class="text-sm">Republic of the Philippines</p>
                <p class="font-bold text-lg -my-1">Camarines Sur Polytechnic Colleges</p>
                <p class="text-sm">Nabua, Camarines Sur</p>
                <p class="italic text-xs text-gray-600">Academic Year <span id="displayAcademicYear"><?= date('Y') . '-' . (date('Y') + 1) ?></span></p>
                <p class="font-bold text-xl mt-2">SUPREME STUDENT COUNCIL</p>
            </div>
            <div class="w-24">
                <img src="<?= base_url('assets\SSC Logo.png') ?>" alt="SSC Logo" class="w-full">
            </div>
        </div>
        
        <hr class="border-2 border-blue-800 my-2">
        
        <!-- Certificate Title -->
        <div class="text-center my-6 times-new-roman">
            <h1 class="text-2xl font-bold">OFFICIAL E-VOTING RESULTS</h1>
            <p class="text-gray-600 -px-4 italic text-xl"><span id="displayDepartment">
                <?php 
                    switch($election['Department']) {
                        case 0: echo "Camarines Sur Polytechnic Colleges"; break;
                        case 1: echo "College of Computer Studies"; break;
                        case 2: echo "College of Engineering and Architecture"; break;
                        case 3: echo "College of Health Sciences"; break;
                        case 4: echo "College of Tourism, Hospitality and Business Management"; break;
                        case 5: echo "College of Technological and Developmental Education"; break;
                        case 6: echo "College of Arts and Sciences"; break;
                        default: echo "Department";
                    }
                ?>
            </span></p>
            <h2 class="text-lg font-semibold"><?= htmlspecialchars($election['ElectionName']) ?></h2>
        </div>
        
        <!-- Election Results -->
        <div class="">
            <h2 class="text-xl times-new-roman font-bold text-center border-b-2 border-gray-800 pb-2 mb-4">ELECTION RESULTS</h2>
            
            <?php foreach ($resultsByPosition as $positionId => $positionData): ?>
            <div class="">
                <table class="w-full border-collapse border border-gray-300 arial">
                    <thead>
                        <tr>
                            <th class="border border-gray-300 p-2 w-1/3 text-left times-new-roman">POSITION</th>
                            <th class="border border-gray-300 p-2 w-1/3 text-left times-new-roman">CANDIDATE NAME</th>
                            <th class="border border-gray-300 p-2 w-1/6 text-center times-new-roman">VOTES</th>
                            <th class="border border-gray-300 p-2 w-1/6 text-center times-new-roman">PERCENTAGE</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $rowCount = count($positionData['candidates']); ?>
                        <?php foreach ($positionData['candidates'] as $index => $candidate): ?>
                            <tr>
                                <?php if ($index === 0): ?>
                                    <td class="p-1 border border-gray-300 align-middle times-new-roman" rowspan="<?= $rowCount ?>">
                                        <?= htmlspecialchars($positionData['positionName']) ?>
                                    </td>
                                <?php endif; ?>
                                
                                <td class="p-1 border border-gray-300 <?= $candidate['name'] === 'Abstain' ? 'bg-red-100' : '' ?> times-new-roman">
                                    <?= htmlspecialchars($candidate['name']) ?>
                                </td>
                                <td class="p-1 border border-gray-300 text-center arial">
                                    <?= $candidate['votes'] ?>
                                </td>
                                <td class="p-1 border border-gray-300 text-center arial">
                                    <?= $candidate['percentage'] ?>%
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <?php endforeach; ?>
        </div>
        
        <!-- Election Summary -->
        <div class="my-6">
            <h2 class="text-xl times-new-roman font-bold text-center border-b-2 border-gray-800 pb-2 mb-4">ELECTION SUMMARY</h2>
            
            <table class="w-full border-collapse border border-gray-300">
                <tbody>
                    <tr>
                        <td class="border border-gray-300 w-2/3 times-new-roman">Total Number of Registered Voters</td>
                        <td class="border border-gray-300 w-1/3 text-center arial"><?= $electionStats['totalVoters'] ?></td>
                    </tr>
                    <tr>
                        <td class="border border-gray-300 times-new-roman">Total Number of Votes Cast</td>
                        <td class="border border-gray-300 text-center arial"><?= $electionStats['totalVotes'] ?></td>
                    </tr>
                    <tr>
                        <td class="border border-gray-300 times-new-roman">Voter Turnout</td>
                        <td class="border border-gray-300 text-center arial"><?= $electionStats['turnout'] ?>%</td>
                    </tr>
                    <tr>
                        <td class="border border-gray-300 times-new-roman">Start of Election</td>
                        <td class="border border-gray-300 text-center arial"><?= $electionStats['startDate'] ?></td>
                    </tr>
                    <tr>
                        <td class="border border-gray-300 times-new-roman">End of Election</td>
                        <td class="border border-gray-300 text-center arial"><?= $electionStats['endDate'] ?></td>
                    </tr>
                </tbody>
            </table>
            <p class="text-sm mt-2 text-right text-gray-500">Generated on: <?= date('F j, Y g:i A', strtotime('+8 hours')) ?></p>
        </div>
        
        <!-- Certification -->
        <div class="my-6">
            <h2 class="text-lg times-new-roman font-bold mb-2">Certification</h2>
            <p class="mb-4 arial">
                We hereby certify that the above results are true and correct based on the Electronic 
                Voting System used for the Supreme Student Council Election of the 
                <span id="certificationDepartment">
                    <?php 
                        switch($election['Department']) {
                            case 0: echo "Camarines Sur Polytechnic Colleges"; break;
                            case 1: echo "College of Computer Studies"; break;
                            case 2: echo "College of Engineering and Architecture"; break;
                            case 3: echo "College of Health Sciences"; break;
                            case 4: echo "College of Tourism, Hospitality and Business Management"; break;
                            case 5: echo "College of Technological and Developmental Education"; break;
                            case 6: echo "College of Arts and Sciences"; break;
                            default: echo "Department";
                        }
                    ?>
                </span> at Camarines Sur Polytechnic Colleges.
            </p>
            
            <!-- Representative Signature Lines -->
            <div class="flex flex-wrap justify-center gap-8 mt-16">
                <?php foreach ($representatives as $rep): ?>
                <div class="text-center w-64">
                    <div class="border-t border-black pt-1">
                        <p class="font-bold times-new-roman"><?= htmlspecialchars($rep['name']) ?></p>
                        <p class="text-sm times-new-roman"><?= htmlspecialchars($rep['title']) ?></p>
                        <p class="text-sm times-new-roman"><?= htmlspecialchars($rep['organization']) ?></p>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
   