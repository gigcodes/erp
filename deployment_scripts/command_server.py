from flask import Flask, request, jsonify
import subprocess

app = Flask(__name__)

@app.route('/execute', methods=['POST'])
def execute_command():
    data = request.get_json()
    command = data.get('command')
    server = data.get('server')
    magdir = data.get('dir')
    bashscript = "/root/commands.sh -c \"" + command + "\" -s \"" + server + "\" -d \"" + magdir + "\""
    print(bashscript)
    
    try:
        result = subprocess.run(bashscript, shell=True, text=True, stdout=subprocess.PIPE, stderr=subprocess.PIPE)
        
        if result.returncode == 0:
            response = {
                'success': True,
                'stdout': result.stdout
            }
        else:
            response = {
                'success': False,
                'stderr': result.stderr
            }
    except subprocess.CalledProcessError as e:
        response = {
            'success': False,
            'error': str(e)
        }
    
    return jsonify(response)


if __name__ == '__main__':
    app.run(host='0.0.0.0', port=5000)

#if __name__ == '__main__':
#    app.run(debug=True)
