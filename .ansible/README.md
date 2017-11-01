## How to use ANSIBLE

### Install [Ansible](https://www.ansible.com)

- Ansible for linux :

    Type these command in terminal :
    
        $ sudo apt-get install software-properties-common
        $ sudo apt-add-repository ppa:ansible/ansible
        $ sudo apt-get update
        $ sudo apt-get install ansible

    Check if correctly installed :
    
        $ ansible --version
        >= 2.1

### Use it

- Run this command :

    
    ansible-playbook -i .ansible/hosts/hosts .ansible/playbook.yml --limit "preprod" --ask-become-pass
