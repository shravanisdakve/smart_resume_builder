// regex for validation
const strRegex =  /^[a-zA-Z\s]*$/; // containing only letters
const emailRegex = /^(([^<>()\[\]\\.,;:\s@"]+(\.[^<>()\[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
const phoneRegex = /^[\+]?[(]?[0-9]{3}[)]?[-\s\.]?[0-9]{3}[-\s\.]?[0-9]{4,6}$/im;
/* supports following number formats - (123) 456-7890, (123)456-7890, 123-456-7890, 123.456.7890, 1234567890, +31636363634, 075-63546725 */
const digitRegex = /^\d+$/;

const mainForm = document.getElementById('cv-form');
const validType = {
    TEXT: 'text',
    TEXT_EMP: 'text_emp',
    EMAIL: 'email',
    DIGIT: 'digit',
    PHONENO: 'phoneno',
    ANY: 'any',
}

// user inputs elements
let firstnameElem = mainForm.firstname,
    middlenameElem = mainForm.middlename,
    lastnameElem = mainForm.lastname,
    imageElem = mainForm.image,
    designationElem = mainForm.designation,
    addressElem = mainForm.address,
    emailElem = mainForm.email,
    phonenoElem = mainForm.phoneno,
    summaryElem = mainForm.summary;

// display elements
let nameDsp = document.getElementById('fullname_dsp'),
    imageDsp = document.getElementById('image_dsp'),
    phonenoDsp = document.getElementById('phoneno_dsp'),
    emailDsp = document.getElementById('email_dsp'),
    addressDsp = document.getElementById('address_dsp'),
    designationDsp = document.getElementById('designation_dsp'),
    summaryDsp = document.getElementById('summary_dsp'),
    projectsDsp = document.getElementById('projects_dsp'),
    achievementsDsp = document.getElementById('achievements_dsp'),
    skillsDsp = document.getElementById('skills_dsp'),
    educationsDsp = document.getElementById('educations_dsp'),
    experiencesDsp = document.getElementById('experiences_dsp');

// first value is for the attributes and second one passes the nodelists
const fetchValues = (attrs, ...nodeLists) => {
    let elemsAttrsCount = nodeLists.length;
    let elemsDataCount = nodeLists[0].length;
    let tempDataArr = [];

    // first loop deals with the no of repeaters value
    for(let i = 0; i < elemsDataCount; i++){
        let dataObj = {}; // creating an empty object to fill the data
        // second loop fetches the data for each repeaters value or attributes 
        for(let j = 0; j < elemsAttrsCount; j++){
            // setting the key name for the object and fill it with data
            dataObj[`${attrs[j]}`] = nodeLists[j][i].value;
        }
        tempDataArr.push(dataObj);
    }

    return tempDataArr;
}

const getUserInputs = () => {

    // achivements 
    let achievementsTitleElem = document.querySelectorAll('.achieve_title'),
    achievementsDescriptionElem = document.querySelectorAll('.achieve_description');

    // experiences
    let expTitleElem = document.querySelectorAll('.exp_title'),
    expOrganizationElem = document.querySelectorAll('.exp_organization'),
    expLocationElem = document.querySelectorAll('.exp_location'),
    expStartDateElem = document.querySelectorAll('.exp_start_date'),
    expEndDateElem = document.querySelectorAll('.exp_end_date'),
    expDescriptionElem = document.querySelectorAll('.exp_description');

    // education
    let eduSchoolElem = document.querySelectorAll('.edu_school'),
    eduDegreeElem = document.querySelectorAll('.edu_degree'),
    eduCityElem = document.querySelectorAll('.edu_city'),
    eduStartDateElem = document.querySelectorAll('.edu_start_date'),
    eduGraduationDateElem = document.querySelectorAll('.edu_graduation_date'),
    eduDescriptionElem = document.querySelectorAll('.edu_description');

    let projTitleElem = document.querySelectorAll('.proj_title'),
    projLinkElem = document.querySelectorAll('.proj_link'),
    projDescriptionElem = document.querySelectorAll('.proj_description');

    let skillElem = document.querySelectorAll('.skill');

    // event listeners for form validation
    firstnameElem.addEventListener('keyup', (e) => validateFormData(e.target, validType.TEXT, 'First Name'));
    middlenameElem.addEventListener('keyup', (e) => validateFormData(e.target, validType.TEXT_EMP, 'Middle Name'));
    lastnameElem.addEventListener('keyup', (e) => validateFormData(e.target, validType.TEXT, 'Last Name'));
    phonenoElem.addEventListener('keyup', (e) => validateFormData(e.target, validType.PHONENO, 'Phone Number'));
    emailElem.addEventListener('keyup', (e) => validateFormData(e.target, validType.EMAIL, 'Email'));
    addressElem.addEventListener('keyup', (e) => validateFormData(e.target, validType.ANY, 'Address'));
    designationElem.addEventListener('keyup', (e) => validateFormData(e.target, validType.TEXT, 'Designation'));

    achievementsTitleElem.forEach(item => item.addEventListener('keyup', (e) => validateFormData(e.target, validType.ANY, 'Title')));
    achievementsDescriptionElem.forEach(item => item.addEventListener('keyup', (e) => validateFormData(e.target, validType.ANY, 'Description')));
    expTitleElem.forEach(item => item.addEventListener('keyup', (e) => validateFormData(e.target, validType.ANY, 'Title')));
    expOrganizationElem.forEach(item => item.addEventListener('keyup', (e) => validateFormData(e.target, validType.ANY, 'Organization')));
    expLocationElem.forEach(item => item.addEventListener('keyup', (e) => validateFormData(e.target, validType.ANY, "Location")));
    expStartDateElem.forEach(item => item.addEventListener('blur', (e) => validateFormData(e.target, validType.ANY, 'End Date')));
    expEndDateElem.forEach(item => item.addEventListener('keyup', (e) => validateFormData(e.target, validType.ANY, 'End Date')));
    expDescriptionElem.forEach(item => item.addEventListener('keyup', (e) => validateFormData(e.target, validType.ANY, 'Description')));
    eduSchoolElem.forEach(item => item.addEventListener('keyup', (e) => validateFormData(e.target, validType.ANY, 'School')));
    eduDegreeElem.forEach(item => item.addEventListener('keyup', (e) => validateFormData(e.target, validType.ANY, 'Degree')));
    eduCityElem.forEach(item => item.addEventListener('keyup', (e) => validateFormData(e.target, validType.ANY, 'City')));
    eduStartDateElem.forEach(item => item.addEventListener('blur', (e) => validateFormData(e.target, validType.ANY, 'Start Date')));
    eduGraduationDateElem.forEach(item => item.addEventListener('blur', (e) => validateFormData(e.target, validType.ANY, 'Graduation Date')));
    eduDescriptionElem.forEach(item => item.addEventListener('keyup', (e) => validateFormData(e.target, validType.ANY, 'Description')));
    projTitleElem.forEach(item => item.addEventListener('keyup', (e) => validateFormData(e.target, validType.ANY, 'Title')));
    projLinkElem.forEach(item => item.addEventListener('keyup', (e) => validateFormData(e.target, validType.ANY, 'Link')));
    projDescriptionElem.forEach(item => item.addEventListener('keyup', (e) => validateFormData(e.target, validType.ANY, 'Description')));
    skillElem.forEach(item => item.addEventListener('keyup', (e) => validateFormData(e.target, validType.ANY, 'skill')));

    return {
        firstname: firstnameElem.value,
        middlename: middlenameElem.value,
        lastname: lastnameElem.value,
        designation: designationElem.value,
        address: addressElem.value,
        email: emailElem.value,
        phoneno: phonenoElem.value,
        summary: summaryElem.value,
        achievements: fetchValues(['achieve_title', 'achieve_description'], achievementsTitleElem, achievementsDescriptionElem),
        experiences: fetchValues(['exp_title', 'exp_organization', 'exp_location', 'exp_start_date', 'exp_end_date', 'exp_description'], expTitleElem, expOrganizationElem, expLocationElem, expStartDateElem, expEndDateElem, expDescriptionElem),
        educations: fetchValues(['edu_school', 'edu_degree', 'edu_city', 'edu_start_date', 'edu_graduation_date', 'edu_description'], eduSchoolElem, eduDegreeElem, eduCityElem, eduStartDateElem, eduGraduationDateElem, eduDescriptionElem),
        projects: fetchValues(['proj_title', 'proj_link', 'proj_description'], projTitleElem, projLinkElem, projDescriptionElem),
        skills: fetchValues(['skill'], skillElem)
    }
};

function validateFormData(elem, elemType, elemName){
    // checking for text string and non empty string
    if(elemType == validType.TEXT){
        if(!strRegex.test(elem.value) || elem.value.trim().length == 0) addErrMsg(elem, elemName);
        else removeErrMsg(elem);
    }

    // checking for only text string
    if(elemType == validType.TEXT_EMP){
        if(!strRegex.test(elem.value)) addErrMsg(elem, elemName);
        else removeErrMsg(elem);
    }

    // checking for email
    if(elemType == validType.EMAIL){
        if(!emailRegex.test(elem.value) || elem.value.trim().length == 0) addErrMsg(elem, elemName);
        else removeErrMsg(elem);
    }

    // checking for phone number
    if(elemType == validType.PHONENO){
        if(!phoneRegex.test(elem.value) || elem.value.trim().length == 0) addErrMsg(elem, elemName);
        else removeErrMsg(elem);
    }

    // checking for only empty
    if(elemType == validType.ANY){
        if(elem.value.trim().length == 0) addErrMsg(elem, elemName);
        else removeErrMsg(elem);
    }
}

// adding the invalid text
function addErrMsg(formElem, formElemName){
    formElem.nextElementSibling.innerHTML = `${formElemName} is invalid`;
}

// removing the invalid text 
function removeErrMsg(formElem){
    formElem.nextElementSibling.innerHTML = "";
}

// show the list data
const showListData = (listData, listContainer) => {
    listContainer.innerHTML = "";
    listData.forEach(listItem => {
        let itemElem = document.createElement('div');
        itemElem.classList.add('preview-item');
        
        for(const key in listItem){
            let subItemElem = document.createElement('span');
            subItemElem.classList.add('preview-item-val');
            subItemElem.innerHTML = `${listItem[key]}`;
            itemElem.appendChild(subItemElem);
        }

        listContainer.appendChild(itemElem);
    })
}

const displayCV = (userData) => {
    nameDsp.innerHTML = userData.firstname + " " + userData.middlename + " " + userData.lastname;
    phonenoDsp.innerHTML = userData.phoneno;
    emailDsp.innerHTML = userData.email;
    addressDsp.innerHTML = userData.address;
    designationDsp.innerHTML = userData.designation;
    summaryDsp.innerHTML = userData.summary;
    showListData(userData.projects, projectsDsp);
    showListData(userData.achievements, achievementsDsp);
    showListData(userData.skills, skillsDsp);
    showListData(userData.educations, educationsDsp);
    showListData(userData.experiences, experiencesDsp);
}

// generate CV
const generateCV = () => {
    let userData = getUserInputs();
    displayCV(userData);
    resumeCheck();
    console.log(userData);
}

function resumeCheck() {
    const userData = getUserInputs();
    const suggestions = [];

    if (userData.skills.length < 3) {
        suggestions.push('Add at least 3 skills to your resume.');
    }

    if (userData.experiences.length === 0) {
        suggestions.push('Add at least one work experience to your resume.');
    }

    if (userData.summary.trim() === '') {
        suggestions.push('Add a summary to your resume.');
    }

    localStorage.setItem('resumeSuggestions', JSON.stringify(suggestions));
}

function previewImage(){
    let oFReader = new FileReader();
    oFReader.readAsDataURL(imageElem.files[0]);
    oFReader.onload = function(ofEvent){
        imageDsp.src = ofEvent.target.result;
    }
}


// --- Start of templates.js content ---
function initializeTemplateSelection() {
    const resumeData = JSON.parse(localStorage.getItem('resumeDraft'));
    const previewPanel = document.getElementById('resume-preview');
    const thumbnails = document.querySelectorAll('.template-selection-section .thumbnail'); // Select thumbnails within the new section

    function generateResumeHTML(template) {
        let html = `<div class="${template}-template">`;

        if(resumeData) { // Check if resumeData exists
            if(template === 'harvard'){
                html += `
                    <h1>${resumeData.firstname} ${resumeData.middlename} ${resumeData.lastname}</h1>
                    <p>${resumeData.address} | ${resumeData.phoneno} | ${resumeData.email}</p>
                    <h2>Summary</h2>
                    <p>${resumeData.summary}</p>
                    <h2>Experience</h2>
                    ${resumeData.experiences.map(exp => `
                        <div>
                            <h3>${exp.exp_title}</h3>
                            <p><strong>${exp.exp_organization}</strong> | ${exp.exp_location} | ${exp.exp_start_date} - ${exp.exp_end_date}</p>
                            <ul><li>${exp.exp_description.replace(/\n/g, "</li><li>")}</li></ul>
                        </div>
                    `).join('')}
                    <h2>Education</h2>
                    ${resumeData.educations.map(edu => `
                        <div>
                            <h3>${edu.edu_degree}</h3>
                            <p><strong>${edu.edu_school}</strong> | ${edu.edu_city} | ${edu.edu_start_date} - ${edu.edu_graduation_date}</p>
                            <ul><li>${edu.edu_description.replace(/\n/g, "</li><li>")}</li></ul>
                        </div>
                    `).join('')}
                    <h2>Skills</h2>
                    <ul>${resumeData.skills.map(skill => `<li>${skill.skill}</li>`).join('')}</ul>
                `;
            } else if (template === 'swiss'){
                html += `
                    <div class="left-column">
                        <h1>${resumeData.firstname} ${resumeData.middlename} ${resumeData.lastname}</h1>
                        <p>${resumeData.designation}</p>
                        <h2>About Me</h2>
                        <p>${resumeData.summary}</p>
                        <h2>Contact</h2>
                        <p>${resumeData.address}</p>
                        <p>${resumeData.phoneno}</p>
                        <p>${resumeData.email}</p>
                        <h2>Skills</h2>
                        <ul>${resumeData.skills.map(skill => `<li>${skill.skill}</li>`).join('')}</ul>
                    </div>
                    <div class="right-column">
                        <h2>Experience</h2>
                        ${resumeData.experiences.map(exp => `
                            <div>
                                <h3>${exp.exp_title}</h3>
                                <p><strong>${exp.exp_organization}</strong> | ${exp.exp_location} | ${exp.exp_start_date} - ${exp.exp_end_date}</p>
                                <ul><li>${exp.exp_description.replace(/\n/g, "</li><li>")}</li></ul>
                            </div>
                        `).join('')}
                        <h2>Education</h2>
                        ${resumeData.educations.map(edu => `
                            <div>
                                <h3>${edu.edu_degree}</h3>
                                <p><strong>${edu.edu_school}</strong> | ${edu.edu_city} | ${edu.edu_start_date} - ${edu.edu_graduation_date}</p>
                                <ul><li>${edu.edu_description.replace(/\n/g, "</li><li>")}</li></ul>
                            </div>
                        `).join('')}
                        <h2>Projects</h2>
                        ${resumeData.projects.map(proj => `
                            <div>
                                <h3>${proj.proj_title}</h3>
                                <p><a href="${proj.proj_link}">${proj.proj_link}</a></p>
                                <ul><li>${proj.proj_description.replace(/\n/g, "</li><li>")}</li></ul>
                            </div>
                        `).join('')}
                    </div>
                `;
            } else if (template === 'pinnacle'){
                html += `
                    <div class="header">
                        <h1>${resumeData.firstname} ${resumeData.middlename} ${resumeData.lastname}</h1>
                        <p>${resumeData.designation}</p>
                    </div>
                    <p>${resumeData.address} | ${resumeData.phoneno} | ${resumeData.email}</p>
                    <h2>Summary</h2>
                    <p>${resumeData.summary}</p>
                    <h2>Experience</h2>
                    ${resumeData.experiences.map(exp => `
                        <div>
                            <h3>${exp.exp_title}</h3>
                            <p><strong>${exp.exp_organization}</strong> | ${exp.exp_location} | ${exp.exp_start_date} - ${exp.exp_end_date}</p>
                            <ul><li>${exp.exp_description.replace(/\n/g, "</li><li>")}</li></ul>
                        </div>
                    `).join('')}
                    <h2>Education</h2>
                    ${resumeData.educations.map(edu => `
                        <div>
                            <h3>${edu.edu_degree}</h3>
                            <p><strong>${edu.edu_school}</strong> | ${edu.edu_city} | ${edu.edu_start_date} - ${edu.edu_graduation_date}</p>
                            <ul><li>${edu.edu_description.replace(/\n/g, "</li><li>")}</li></ul>
                        </div>
                    `).join('')}
                    <h2>Skills</h2>
                    <ul>${resumeData.skills.map(skill => `<li>${skill.skill}</li>`).join('')}</ul>
                `;
                        } else if (template === 'elegant'){
                            html += `
                                <div class="header" style="background-color: #f0f0f0; color: #333;">
                                    <h1>${resumeData.firstname} ${resumeData.middlename} ${resumeData.lastname}</h1>
                                    <p>${resumeData.designation}</p>
                                </div>
                                <div style="padding: 20px;">
                                    <p style="text-align: center;">${resumeData.address} | ${resumeData.phoneno} | ${resumeData.email}</p>
                                    <h2>Summary</h2>
                                    <p>${resumeData.summary}</p>
                                    <h2>Experience</h2>
                                    ${resumeData.experiences.map(exp => `
                                        <div>
                                            <h3>${exp.exp_title}</h3>
                                            <p><strong>${exp.exp_organization}</strong> | ${exp.exp_location} | ${exp.exp_start_date} - ${exp.exp_end_date}</p>
                                            <ul><li>${exp.exp_description.replace(/\n/g, "</li><li>")}</li></ul>
                                        </div>
                                    `).join('')}
                                    <h2>Education</h2>
                                    ${resumeData.educations.map(edu => `
                                        <div>
                                            <h3>${edu.edu_degree}</h3>
                                            <p><strong>${edu.edu_school}</strong> | ${edu.edu_city} | ${edu.edu_start_date} - ${edu.edu_graduation_date}</p>
                                            <ul><li>${edu.edu_description.replace(/\n/g, "</li><li>")}</li></ul>
                                        </div>
                                    `).join('')}
                                    <h2>Skills</h2>
                                    <ul>${resumeData.skills.map(skill => `<li>${skill.skill}</li>`).join('')}</ul>
                                </div>
                            `;
            } else if (template === 'creative'){
                html += `
                    <div style="background-color: #ff7f50; color: white; padding: 20px; text-align: center;">
                        <h1>${resumeData.firstname} ${resumeData.middlename} ${resumeData.lastname}</h1>
                        <p>${resumeData.designation}</p>
                    </div>
                    <div style="padding: 20px;">
                        <p style="text-align: center;">${resumeData.address} | ${resumeData.phoneno} | ${resumeData.email}</p>
                        <h2>Summary</h2>
                        <p>${resumeData.summary}</p>
                        <h2>Experience</h2>
                        ${resumeData.experiences.map(exp => `
                            <div>
                                <h3>${exp.exp_title}</h3>
                                <p><strong>${exp.exp_organization}</strong> | ${exp.exp_location} | ${exp.exp_start_date} - ${exp.exp_end_date}</p>
                                <ul><li>${exp.exp_description.replace(/\n/g, "</li><li>")}</li></ul>
                            </div>
                        `).join('')}
                        <h2>Education</h2>
                        ${resumeData.educations.map(edu => `
                            <div>
                                <h3>${edu.edu_degree}</h3>
                                <p><strong>${edu.edu_school}</strong> | ${edu.edu_city} | ${edu.edu_start_date} - ${edu.edu_graduation_date}</p>
                                <ul><li>${edu.edu_description.replace(/\n/g, "</li><li>")}</li></ul>
                            </div>
                        `).join('')}
                        <h2>Skills</h2>
                        <ul>${resumeData.skills.map(skill => `<li>${skill.skill}</li>`).join('')}</ul>
                    </div>
                `;
            } else if (template === 'fresh'){
                html += `
                    <div style="border: 2px solid #4CAF50; padding: 20px;">
                        <h1 style="color: #4CAF50;">${resumeData.firstname} ${resumeData.middlename} ${resumeData.lastname}</h1>
                        <p>${resumeData.designation}</p>
                        <p>${resumeData.address} | ${resumeData.phoneno} | ${resumeData.email}</p>
                        <h2>Summary</h2>
                        <p>${resumeData.summary}</p>
                        <h2>Experience</h2>                                        ${resumeData.experiences.map(exp => `
                                            <div>
                                                <h3>${exp.exp_title}</h3>
                                                <p><strong>${exp.exp_organization}</strong> | ${exp.exp_location} | ${exp.exp_start_date} - ${exp.exp_end_date}</p>
                                                <ul><li>${exp.exp_description.replace(/\n/g, "</li><li>")}</li></ul>
                                            </div>
                                        `).join('')}
                                        <h2>Education</h2>
                                        ${resumeData.educations.map(edu => `
                                            <div>
                                                <h3>${edu.edu_degree}</h3>
                                                <p><strong>${edu.edu_school}</strong> | ${edu.edu_city} | ${edu.edu_start_date} - ${edu.edu_graduation_date}</p>
                                                <ul><li>${edu.edu_description.replace(/\n/g, "</li><li>")}</li></ul>
                                            </div>
                                        `).join('')}
                                        <h2>Skills</h2>
                                        <ul>${resumeData.skills.map(skill => `<li>${skill.skill}</li>`).join('')}</ul>
                                    </div>
                                `;
                            }        } else {
            html += `<p>No resume data available. Please fill out the form.</p>`;
        }

        html += `</div>`;
        return html;
    }

    // This is the main page
    thumbnails.forEach(thumbnail => {
        const template = thumbnail.dataset.template;
        const iframe = document.createElement('iframe');
        iframe.src = `templates.html?template=${template}`; // Still use templates.html for iframe content
        thumbnail.appendChild(iframe);

        thumbnail.addEventListener('click', () => {
            updatePreview(template);
            thumbnails.forEach(t => t.classList.remove('active'));
            thumbnail.classList.add('active');
        });
    });

    function updatePreview(template) {
        previewPanel.classList.add('fade-out');
        setTimeout(() => {
            previewPanel.innerHTML = generateResumeHTML(template);
            previewPanel.classList.remove('fade-out');
            previewPanel.classList.add('fade-in');
        }, 500);
    }

    // Initial preview
    updatePreview('harvard');
    document.querySelector('.template-selection-section .thumbnail[data-template="harvard"]').classList.add('active');

    // Download functionality
    document.getElementById('download-pdf').addEventListener('click', () => {
        const element = previewPanel.firstChild;
        html2pdf(element);
    });

    document.getElementById('print-cv').addEventListener('click', () => {
        window.print();
    });

    const suggestionsModal = document.getElementById('suggestions-modal');
    const getSuggestionsBtn = document.getElementById('get-suggestions');
    const closeBtn = document.querySelector('.close-button');

    getSuggestionsBtn.addEventListener('click', () => {
        const suggestions = JSON.parse(localStorage.getItem('resumeSuggestions'));
        const suggestionsContent = document.getElementById('suggestions-content');
        suggestionsContent.innerHTML = '';
        if (suggestions && suggestions.length > 0) {
            const ul = document.createElement('ul');
            suggestions.forEach(suggestion => {
                const li = document.createElement('li');
                li.textContent = suggestion;
                ul.appendChild(li);
            });
            suggestionsContent.appendChild(ul);
        } else {
            suggestionsContent.innerHTML = '<p>No suggestions at the moment. Your resume looks great!</p>';
        }
        suggestionsModal.style.display = 'block';
    });

    closeBtn.addEventListener('click', () => {
        suggestionsModal.style.display = 'none';
    });

    window.addEventListener('click', (event) => {
        if (event.target == suggestionsModal) {
            suggestionsModal.style.display = 'none';
        }
    });
// --- End of templates.js content ---
}

document.addEventListener('DOMContentLoaded', function() {
    const prevBtn = document.querySelector('.prev-btn');
    const nextBtn = document.querySelector('.next-btn');
    const formSteps = document.querySelectorAll('.form-step');
    const progressSteps = document.querySelectorAll('.progress-bar-step');

    let currentStep = 0;

    function updateFormSteps() {
        formSteps.forEach((step, index) => {
            if (index === currentStep) {
                step.classList.add('active');
            } else {
                step.classList.remove('active');
            }
        });

        progressSteps.forEach((step, index) => {
            if (index === currentStep) {
                step.classList.add('active');
            } else {
                step.classList.remove('active');
            }
        });

        if (currentStep === 0) {
            prevBtn.style.display = 'none';
        } else {
            prevBtn.style.display = 'block';
        }

        if (currentStep === formSteps.length - 1) {
            nextBtn.textContent = 'Finish';
        } else {
            nextBtn.textContent = 'Next';
        }
    }

    nextBtn.addEventListener('click', () => {
        if (currentStep < formSteps.length - 1) {
            currentStep++;
            updateFormSteps();
        } else {
            // Show template selection section
            document.querySelector('.multi-step-form').style.display = 'none';
            document.querySelector('.template-selection-section').style.display = 'block';
            initializeTemplateSelection(); // Call function to initialize template selection
        }
    });

    prevBtn.addEventListener('click', () => {
        if (currentStep > 0) {
            currentStep--;
            updateFormSteps();
        } else {
            // If on the first step and previous is clicked, hide template selection and show form
            document.querySelector('.multi-step-form').style.display = 'block';
            document.querySelector('.template-selection-section').style.display = 'none';
        }
    });

    progressSteps.forEach((step, index) => {
        step.addEventListener('click', () => {
            currentStep = index;
            updateFormSteps();
        });
    });

    updateFormSteps();

    const previewContainer = document.querySelector('.preview-cnt');
    new Sortable(previewContainer, {
        animation: 150,
        ghostClass: 'blue-background-class'
    });
});